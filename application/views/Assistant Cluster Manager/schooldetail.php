<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM APP | WebAPP</title>
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">School Detail</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
<?php 
$call=0;
$visit=0;
$uti=0;
$what=0;
$email=0;
$report=0;
foreach($slog as $sl){
    if($sl->task_type=='Call'){$call++;}
    if($sl->task_type=='Installation'){$visit++;}
    if($sl->task_type=='Utilisation'){$uti++;}
    if($sl->task_type=='Whatsapp'){$what++;}
    if($sl->task_type=='Email'){$email++;}
    if($sl->task_type=='Report'){$report++;}
}
?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
          <div class="col-sm col-md-8 col-lg-8">
            <?php foreach($spd as $d){
                $pcode = $d->project_code;
                $sname = $d->sname;
                $sid = $d->id;
                $pi = $d->pi_id;
                $zh = $d->zh_id;
                $ins = $d->ins_id;
                $pin=$this->Menu_model->get_user_byid($pi);
                $zhn=$this->Menu_model->get_user_byid($zh);
                $insn=$this->Menu_model->get_user_byid($ins);
                $program=$this->Menu_model->get_clientbypc($pcode);
                $programid=$program[0]->id;
                $status1 = 'New School';
                $status2 = 'FTTP Done';
                $status3 = 'Active School';
                $status4 = 'Average School';
                $status5 = 'Good School';
                $status6 = 'Model School';
                $status7 = 'Client Readines';
            }
            ?>
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                
                <div class="row">
                    <div class="col-sm col-lg-6"><h4><?=$d->sname?></h4></div>
                    <div class="col-sm col-lg-3"><button type="button" id="add_act" class="btn btn-light"><i class="fa fa-plus"></i> Activity Update</button></div>
                    <div class="col-sm col-lg-3"><button type="button" id="add_wag" class="btn btn-light" value="<?=$d->id?>"><img src="<?=base_url();?>assets/image/icon/whatsapp.png" style="height:30px;"> Group Update</button></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm col-lg-4">Total Logs:</div>
                    <div class="col-sm col-lg-8"><a href="../ProgramPage/<?=$programid?>"><?=$pcode?></a></div>
                    <div class="col-sm col-lg-4">Total Logs:</div>
                    <div class="col-sm col-lg-8"><?php echo sizeof($slog);?></div>
                    <div class="col-sm col-lg-4">Instaltion Person:</div>
                    <div class="col-sm col-lg-8"><?php if($insn){echo $insn[0]->fullname;}?></div>
                    <div class="col-sm col-lg-4">PIA:</div>
                    <div class="col-sm col-lg-8"><?=$pin[0]->fullname?></div>
                    <div class="col-sm col-lg-4">Zonal Head</div>
                    <div class="col-sm col-lg-8"><?=$zhn[0]->fullname?></div>
                    <div class="col-sm col-lg-4">Status Details:</div>
                    <div class="col-sm col-lg-8">
                        <?php foreach($status as $st){
                            $status = $st->name;
                            $color = $st->color;
                            ?>
                        <span class="badge <?=$color?>"><?=$status?> &nbsp;<span class="badge bg-light"><?=$this->Menu_model->get_scount($sid,$status)?></span></span>
                        <?php } ?>
                        <br><br>
                    </div>
                    
                
                    
                    <div class="col-sm col-lg-4">Activity Details:</div>
                    <div class="col-sm col-lg-8">
                        <span class="badge bg-light">Call &nbsp;<span class="badge bg-light"><?=$call?></span></span>
                        <span class="badge bg-secondary">Visit &nbsp;<span class="badge bg-light"><?=$visit?></span></span>
                        <span class="badge bg-info">Utilisation &nbsp;<span class="badge bg-light"><?=$uti?></span></span>
                        <span class="badge bg-warning">Whatsapp &nbsp;<span class="badge bg-light"><?=$what?></span></span>
                        <span class="badge bg-success">Email &nbsp;<span class="badge bg-light"><?=$email?></span></span>
                        <span class="badge bg-light">Report &nbsp;<span class="badge bg-light"><?=$report?></span></span>
                    </div>
                <hr>
                    <div class="col-sm col-lg-4">Email:</div>
                    <div class="col-sm col-lg-8"><?=$d->email?></div>
                <hr>
                    <div class="col-sm col-lg-4">Phone:</div>
                    <div class="col-sm col-lg-8"><?=$d->contact_no?>
                        <span class="company_contact contact{{cc2.id}}">
                            <a href="tel:+91<?=$d->contact_no?>" target="_blank" style="padding:7px;border-radius:20px;">
                                <img src="<?=base_url();?>assets/image/icon/call.png" style="height:30px;">
                            </a>
                            <a href="https://wa.me/91<?=$d->contact_no?>" target="_blank" style="padding:7px;border-radius:20px;">
                                <img src="<?=base_url();?>assets/image/icon/whatsapp.png" style="height:30px;">
                            </a>
                                            </span>
                    </div>
                <hr>
                    <div class="col-sm col-lg-4">Website:</div>
                    <div class="col-sm col-lg-8"><?=$d->website?></div>
                <hr>
                    <div class="col-sm col-lg-4">Address:</div>
                    <div class="col-sm col-lg-8"><?=$d->saddress?></div>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            
            </div>
          <!-- /.col -->
          <div class="col-sm col-md-4 col-lg-4 showdata">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <div class="row">
                    <div class="col-sm col-lg-5"><h4>Contact</h4></div>
                    <div class="col-sm col-lg-7"><button type="button" id="add_cont" class="btn btn-light" value="<?=$d->id?>" style="float:right;"><i class="fa fa-plus"></i> Add Contact</button></div>
                </div><hr>
                <?php foreach($dataa as $d){ ?>
                    <div class="col-sm">Name : <?=$d->contact_name?></div>
                    <div class="col-sm">Designation : <?=$d->designation?></div>
                    <div class="col-sm">Contact No : <?=$d->contact_no?></div>
                    <div class="col-sm">Email : <?=$d->email?></div><hr>
                <?php } ?>
              </div>
              </div>
              <!-- /.card-header -->
              
              
            </div>
            <!-- /.card -->
  </div>
  
  
  <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Logs</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">                            

                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>								
                                            <th>Sr Number</th>
                                            <th>Plan Date</th>
                                            <th>Completed Date</th>
                                            <th>School Name</th></th>
                                            <th>Task Type</th>
                                            <th>Task Purpose</th>
                                            <th>Task Done By</th>
                                            <th>Remark</th>
                                            <th>Media</th>
                                            <th>Report</th>
                                            <th>Visit Media</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php $i=1; foreach($slog as $sl){
                                      $sid = $sl->sid;
                                      $tid = $sl->taskid;
                                      $status = $sl->status;
                                      $plan=$this->Menu_model->get_plantaskbytid($tid);
                                      $task=$this->Menu_model->get_taskassign_byid($tid);
                                      $nstatus=$this->Menu_model->get_snextststus($status);
                                      $uid = $task[0]->to_user;
                                      $user=$this->Menu_model->get_user_byid($uid);
                                      $report=$this->Menu_model->get_reportbystid($tid,$sid);
                                      ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$plan[0]->sdatet?></td>
                                        <td><?=$plan[0]->donet?></td>
                                        <td><?=$sname?></td>
                                        <td><?=$sl->task_type?></td>
                                        <td><?=$task[0]->task_subtype?></td>
                                        <td><?=$user[0]->fullname?></td>
                                        <td><?=$sl->remark?></td>
                                        <td><?php if($task[0]->task_type=='Utilisation'){?><a href="../ZipDownload/<?=$tid?>"><i class="fa fa-file-archive-o" aria-hidden="true"></i></a><?php }?></td>
                                        <td><?php if($task[0]->task_type=='Report'){?><a href="https://stemoppapp.in/<?=$report[0]->filepath?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i><?php }?>
                                        </td>
                                        <td><?php if($task[0]->task_type=='Visit'){?><a href="../VisitZipDownload/<?=$tid?>"><i class="fa fa-file-archive-o" aria-hidden="true"></i></a><?php }?></td>
                                    </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
            </div>
            <hr />
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
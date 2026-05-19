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


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
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
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <fieldset>
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <div class="pdf-viwer">
                                    
                                <?php if($code==1){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Not Started Day </th>
                                            <th>Warning</th>
                                            <th>Absent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->name?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->uuid?>/Warning/1/<?=$uid?>">Warn</a></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->uuid?>/Absent/1/<?=$uid?>">Absent</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                <?php if($code==2){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Day Start Review not Taken</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->name?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->uuid?>/Warning/2/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                <?php if($code==3){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Not Closed Shift a Day Before</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->name?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->uuid?>/Warning/3/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                    
                                    
                                <?php if($code==4){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Day Close Review not Taken</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->name?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->uuid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($code==5){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Plan but not Completed</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($code==6){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>MOM Plan but not Completed</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($code==7){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (09am to 11am)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($code==8){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (11am to 01pm)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($code==9){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (01pm to 03pm)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                <?php if($code==10){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (03pm to 05pm)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                <?php if($code==11){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (05pm to 07pm)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                <?php if($code==12){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (07pm to 09pm)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                
                                <?php if($code==13){?>
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Task Pending for Completion (07pm to 09pm)</th>
                                            <th>Company Name</th>
                                            <th>Plan Time</th>
                                            <th>Task Type</th>
                                            <th>Warning</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i=1;
                                        $mdata = $this->Menu_model->get_alertreply($uid,$code);
                                        foreach($mdata as $md){?>
                                        <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->uname?></td>
                                        <td><?=$md->compname?></td>
                                        <td><?=$md->appointmentdatetime?></td>
                                        <td><?=$md->aname?></td>
                                        <td><a href="<?=base_url();?>/Menu/alreply/<?=$md->tid?>/Warning/4/<?=$uid?>">Warn</a></td>
                                        </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php } ?>
                                
                                
                                
                                
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
            
        </div>
    </div></div></div></div>
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
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
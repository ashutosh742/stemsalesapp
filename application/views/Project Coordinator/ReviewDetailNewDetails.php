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
       
      <?php 
      $reviewtype = $mreviews[0]->reviewtype;
      $revuid = $mreviews[0]->uid;
      $bdid = $mreviews[0]->bdid;
      $statusData = $this->Menu_model->get_statusbyid($rev_status);
      $statusname = $statusData[0]->name;
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

        <div class="content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12 card bg-primary text-center p-2">
                <h1>Review Report</h1>
                <h4><?= $reviewtype; ?> (<?= $statusname; ?>)</h4>
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
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                          <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                    <tr>
                                      <th>S.No.</th>
                                      <th>Date</th>
                                      <th>BD Name</th>
                                      <th>PST Name</th>
                                      <th>Client Name</th>
                                      <th>Remark</th>
                                      <th>Assign Task</th>
                                      <th>Assign Task Date</th>
                                      <th>Expacted Status Date</th>
                                      <th>Expacted Status</th>
                                      <th>Review Time Status</th>
                                      <th>Current Status</th>
                                      <th>View Report</th>
                                    </tr>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1;
                                      foreach($mdatarev as $md){
                                          $inid = $md->inid;
                                          $ntid = $md->ntid;
                                          $mainrevid = $md->id;
                                          $mainrevid_sdate = $md->sdate;
                                          
                                          $ntid = $this->Menu_model->get_lasttask($ntid);
                                          $psti = $this->Menu_model->get_initbyid($inid);
                                          $pstid = $psti[0]->apst;
                                          $mainbd = $psti[0]->mainbd;
                                          if($pstid!=''){
                                              $pstname = $this->Menu_model->get_userbyid($pstid);
                                              $pstname = $pstname[0]->name;
                                          }else{$pstname='';}
                                          if($mainbd!=''){
                                              $bdname = $this->Menu_model->get_userbyid($mainbd);
                                              $bdname = $bdname[0]->name;
                                          }else{$bdname='';}
                                          $sdatet=$md->sdatet;
                                          $sdatet = date('d-m-Y  h:i A', strtotime($sdatet));
                                          
                                          if(isset($ntid[0]->date)){$ntid = $ntid[0]->date;
                                          
                                          $ntid = date('d-m-Y  h:i A', strtotime($ntid));
                                          }else{$ntid='Not Data';}
                                          
                                          if(isset($md->exdate)){
                                          $exdate = $md->exdate;
                                          $exdate = date('d-m-Y', strtotime($exdate));}
                                          else{$exdate='Not Data';}
                                      ?>
                                    <tr>
                                      <td><?=$i?></td>
                                      <td><?=$mainrevid_sdate;?></td>
                                      <td><?=$bdname?></td>
                                      <td><?=$pstname?></td>
                                      <td><?=$md->cmpname?></td>
                                      <td><?=$md->remark?></td>
                                      <td><?php if($md->aname!=''){echo $md->aname ;}else{echo 'No Data';}?></td>
                                      <td><?=$ntid?></td>
                                      <td><?=$exdate?></td>
                                      <td><?=$md->exname?></td>
                                      <td><?=$md->rtsname?></td>
                                      <td><?=$md->csname?></td>
                                      <td><a href="<?=base_url();?>Menu/ReviewReportsByUser/<?=$rev_id?>/<?= $md->mainrevid; ?>">View Reports</a></td>
                                    </tr>
                                    </a>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
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
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
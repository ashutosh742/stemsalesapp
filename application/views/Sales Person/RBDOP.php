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
            <h1 class="m-0">Open BD Request</h1>
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
          <button id="grid-view-btn" class="btn border">Grid View</button>
          <button id="list-view-btn" class="btn border">Xls View</button>
          <button id="tabular-view-btn" class="btn border">Tabular View</button>
            <div class="container-fluid card p-5" id="data-container">
                <div class="row text-center" id="grid-view">
                    
                    <?php $BDRequest = $this->Menu_model->get_BDRequestbybdid($uid);
                    foreach($BDRequest as $bdr){if($bdr->status=='0'){$rid=$bdr->id;
                    ?>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <a href="<?=base_url();?>Menu/RBDOPDetail/<?=$rid?>" target="_blank">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                              Request Type<br><b><?=$bdr->request_type?></b><hr>
                              Client Type<br><b><?php if($bdr->onnew=='1'){echo 'New Client';}else{echo 'On Borded';}?></b><hr>
                              Client Name<br><b style="color:#808080"><?=$bdr->cname?></b><hr>
                              BD Name<br><b><?=$bdr->bd_name?></b><hr>
                              Request Date<br><b><?=$this->Menu_model->get_dformat($bdr->sdatet)?></b><hr>
                              Target Date<br><b><?=$this->Menu_model->get_odformat($bdr->targetd)?></b><hr>
                              Remark<br><b><?=$bdr->remark?></b><hr>
                              PIA Assigned<br><b><?php $pia= $this->Menu_model->get_BDRPIAbyrid($rid);?><?=$pia[0]->pianame?></b><hr>
                              Other Detail<br><b>
                              <?php if($bdr->noofschool!=''){?>
                              No of School : <?=$bdr->noofschool?><br>
                              <?php }if($bdr->vlocation!=''){?>
                              Location : <?=$bdr->vlocation?><br>
                              <?php }if($bdr->ngoletter!=''){?>
                              NGO Letter : <?=$bdr->ngoletter?><br>
                              <?php }if($bdr->schooltype!=''){?>
                              School Type : <?=$bdr->schooltype?><br>
                              <?php }?>
                              </b><hr>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                          </div>
                          </a>
                        </div>
                       <?php }} ?>
                </div>
                <div id="list-view" style="display: none;">
                    <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.NO.</th>
                                            <th>Request Type</th>
                                            <th>Client Type</th>
                                            <th>Client Name</th>
                                            <th>BD Name</th>
                                            <th>Request Date</th>
                                            <th>Target Date</th>
                                            <th>Remark</th>
                                            <th>PIA Assigned</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $BDRequest = $this->Menu_model->get_BDRequestbybdid($uid);
                                              $i=1; foreach($BDRequest as $bdr){if($bdr->status=='0'){ $rid=$bdr->id;?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><?=$bdr->request_type?></td>
                                            <td><?php if($bdr->onnew=='1'){echo 'New Client';}else{echo 'On Borded';}?></td>
                                            <td><b style="color:#808080"><?=$bdr->cname?></td>
                                            <td><?=$bdr->bd_name?></td>
                                            <td><?=$this->Menu_model->get_dformat($bdr->sdatet)?></td>
                                            <td><?=$this->Menu_model->get_odformat($bdr->targetd)?></td>
                                            <td><?=$bdr->remark?></td>
                                            <td>
                                                <?php $pia= $this->Menu_model->get_BDRPIAbyrid($rid);?>
                                                <?=$pia[0]->pianame?>
                                                
                                            </td>
                                        </tr>
                                        <?php $i++;}} ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </table>
                </div>
                <div id="tabular-view" style="display: none;">
                    3
                </div>    
        </section>  
          
              
                
          </div>
      </div>
    </section>
    
    
    
    <script>
        document.getElementById("grid-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "block";
            document.getElementById("list-view").style.display = "none";
            document.getElementById("list-view-btn").classList.remove('btn-info');
            document.getElementById("grid-view-btn").classList.add('btn-info');
        });
    
        document.getElementById("list-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "none";
            document.getElementById("list-view").style.display = "block";
            document.getElementById("grid-view-btn").classList.remove('btn-info');
            document.getElementById("list-view-btn").classList.add('btn-info');
        });
        
        document.getElementById("tabular-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "none";
            document.getElementById("tabular-view").style.display = "block";
            document.getElementById("grid-view-btn").classList.remove('btn-info');
            document.getElementById("tabular-view-btn").classList.add('btn-info');
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
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

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
                <h3 class="card-title">Status Conversion Detail</h3>
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
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Status Conversion By</th>
                                            <th>Last Status</th>
                                            <th>Current Status</th>
                                            <th>Company Name</th>
                                            <th>BD Name</th>
                                            <th>Total Logs</th>	
                                            <th>Next Action Date</th>
                                            <th>Next Purpose</th>
                                            <th>Next Action Type</th>	
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=1;
                                        if($ab==0){$mdata = $this->Menu_model->get_scong($uid,$tdate,0);}
                                        else{$mdata = $this->Menu_model->get_scong($bdid,$tdate,1);}
                                        foreach($mdata as $m){
                                            $csid = $m->status_id;
                                            $cid = $m->cid_id;
                                            $lid = $m->lastCFID;
                                            
                                            $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                            $ltbl = $this->Menu_model->get_tbldata($lid);
                                            $lsid = $ltbl[0]->status_id;
                                            
                                            if($code==1){$subc=8;}if($code==8){$subc=2;}if($code==2){$subc=3;}
                                            if($code==3){$subc=6;}if($code==6){$subc=9;}if($code==9){$subc=7;}
                                            if($csid!=$lsid){if($code!=10){
                                            if($lsid==$code && $subc==$csid){
                                            $user = $m->user_id;
                                            $user = $this->Menu_model->get_userbyid($user); 
                                            $cd = $this->Menu_model->get_cdbyinid($cid);  
                                            $bd = $cd[0]->creator_id;
                                            $bd = $this->Menu_model->get_userbyid($bd);
                                            
                                            $nid = $m->nextCFID;
                                            $ntbl = $this->Menu_model->get_tbldata($nid);
                                            
                                            $aid  = $ntbl[0]->actiontype_id;
                                            
                                            $csid = $this->Menu_model->get_statusbyid($csid); 
                                            $lsid = $this->Menu_model->get_statusbyid($lsid);
                                            $aid = $this->Menu_model->get_actionbyid($aid);
                                        ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$user[0]->name?></td>
                                        <td><?=$lsid[0]->name?></td>
                                        <td><?=$csid[0]->name?></td>
                                        <td><a href="../../../../CompanyDetails/<?=$cd[0]->id?>"><?=$cd[0]->compname?></a></td>
                                        <td><?=$bd[0]->name?></td>
                                        <td><?=sizeof($tlog)?></td>
                                        <td><?=$ntbl[0]->date?></td>
                                        <td><?=$m->nextaction?></td>
                                        <td><?=$aid[0]->name?></td>
                                     </tr>
                                     <?php $i++;}}else{
                                     $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    
                                    $nid = $m->nextCFID;
                                    $ntbl = $this->Menu_model->get_tbldata($nid);
                                    
                                    $aid  = $ntbl[0]->actiontype_id;
                                    
                                    $csid = $this->Menu_model->get_statusbyid($csid); 
                                    $lsid = $this->Menu_model->get_statusbyid($lsid);
                                    $aid = $this->Menu_model->get_actionbyid($aid);?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$user[0]->name?></td>
                                        <td><?=$lsid[0]->name?></td>
                                        <td><?=$csid[0]->name?></td>
                                        <td><a href="../../../CompanyDetails/<?=$cd[0]->id?>"><?=$cd[0]->compname?></a></td>
                                        <td><?=$bd[0]->name?></td>
                                        <td><?=sizeof($tlog)?></td>
                                        <td><?=$ntbl[0]->date?></td>
                                        <td><?=$m->nextaction?></td>
                                        <td><?=$aid[0]->name?></td>
                                     <?php }}}?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
            </div>
            <hr />
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

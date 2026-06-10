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
            <h1 class="m-0">Status Conversion</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4><?php $uid=$user['user_id']; ?></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
<?php 
$bd = $this->Menu_model->get_userbyaid($uid);
?>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
              <div>
                  <form class="p-3" method="POST" action="SConversion"><input type="date" name="sdate" class="mr-2" value="<?=$sd?>"><input type="date" name="edate" class="mr-2" value="<?=$ed?>">
                    <button type="submit" class="bg-primary text-light">Filter</button></form>
              </div>
    <div class="card">
              <div class="card-header">
                <h3 class="card-title"></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <fieldset>
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <div class="pdf-viwer">
                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Open To Open [RPEM]</th>
                                            <th>Open [RPEM] to Reachout</th>
                                            <th>Reachout to Tentative</th>
                                            <th>Tentative to Positive</th>
                                            <th>Positive To Very Positive</th>
                                            <th>Positive to Closure</th>
                                            <th>Very Positive To Closure</th>
                                            <th>Others</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    for($i = $sdate; $i <= $edate; $i->modify('+1 day')){
                                            $date = $i->format("Y-m-d");
                                            $or=0;$ort=0;$rr=0;$rt=0;$tp=0;$pvp=0;$vph=0;$other=0;
                                            
                                            $sc=$this->Menu_model->get_scong($uid,$date,0);
                                            foreach($sc as $m){
                                                $lid = $m->lastCFID;
                                                $csid = $m->status_id;
                                                $ltbl = $this->Menu_model->get_tbldata($lid);
                                                $lsid = $ltbl[0]->status_id;
                                                if($lsid!=$csid){
                                                if($lsid==1 && $csid ==8){$or++;}
                                                elseif($lsid==8 && $csid ==2){$ort++;}
                                                elseif($lsid==2 && $csid ==3){$rr++;}
                                                elseif($lsid==3 && $csid ==6){$rt++;}
                                                elseif($lsid==6 && $csid ==9){$tp++;}
                                                elseif($lsid==9 && $csid ==7){$pvp++;}
                                                elseif($lsid==7 && $csid ==12){$vph++;}
                                                else{$other++;}
                                            }}
                                            ?>
                                    <tr>
                                        <td><a href="BDSConversion/<?=$date?>"><?=$date?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/1/0"><?=$or?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/8/0"><?=$ort?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/2/0"><?=$rr?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/3/0"><?=$rt?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/6/0"><?=$tp?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/9/0"><?=$pvp?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/7/0"><?=$vph?></a></td>
                                        <td><a href="Conversion/<?=$uid?>/<?=$date?>/10/0"><?=$other?></a></td>
                                    </tr>
                                    <?php } ?>  
                                  </tbody>
                                </table> 
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

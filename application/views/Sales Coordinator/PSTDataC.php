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
            <h1 class="m-0">PST Data</h1>
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
                  <form class="p-3" method="POST" action="PSTDataC"><input type="date" name="sdate" class="mr-2" value="<?=$sd?>"><input type="date" name="edate" class="mr-2" value="<?=$ed?>">
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
                                            <th>Same Status</th>
                                            <th>Very positive to Very positive</th>
                                            <th>Positive to Positive</th>
                                            <th>Tentative to Tentative
                                            <th>NI to NI
                                            <th>Change Status
                                            <th>Tentative to Positive</th>
                                            <th>Positive to Very Positive</th>
                                            <th>Very Positive to Closure</th>
                                            <th>Tentative to WDL</th>
                                            <th>Tentative to NI</th>
                                            <th>Tentative to TTD Reachout</th>
                                            <th>Tentative to WNO Reachout</th> 
                                            <th>Positive to WDL</th>
                                            <th>Very positive to wdl</th>
                                            <th>Other</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    for($i = $sdate; $i <= $edate; $i->modify('+1 day')){
                                            $date = $i->format("Y-m-d");
                                            
                                            $pstcc = $this->Menu_model->get_pstcc($uid,$date,0);
                                                $s=0;$c=0;$vp=0;$p=0;$t=0;$ot=0;$n=0;
                                                $ca=0;$cb=0;$cc=0;$cd=0;$ce=0;$cf=0;$cg=0;$ch=0;$ci=0;
                                                foreach($pstcc as $pscc){
                                                    $psid = $pscc->sid;
                                                    $pnsid = $pscc->nsid;
                                                    
                                                if($psid==$pnsid){$s++;}
                                                if($psid!=$pnsid){$c++;}
                                                if($psid==9 && $pnsid==9){$vp++;}
                                                elseif($psid==6 && $pnsid==6){$p++;}
                                                elseif($psid==3 && $pnsid==3){$t++;}
                                                elseif($psid==5 && $pnsid==5){$n++;}
                                                
                                                elseif($psid==3 && $pnsid==6){$ca++;}
                                                elseif($psid==6 && $pnsid==9){$cb++;}
                                                elseif($psid==9 && $pnsid==7){$cc++;}
                                                elseif($psid==3 && $pnsid==4){$cd++;}
                                                elseif($psid==3 && $pnsid==5){$ce++;}
                                                elseif($psid==3 && $pnsid==10){$cf++;}
                                                elseif($psid==3 && $pnsid==11){$cg++;}
                                                elseif($psid==6 && $pnsid==4){$ch++;}
                                                elseif($psid==9 && $pnsid==4){$ci++;}
                                                else{$ot++;}
                                                
                                                }
                                            
                                            ?>
                                    <tr>
                                        <td><a href="PSTCTeamWork/<?=$date?>"><?=$date?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$s?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$vp?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$p?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$t?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$n?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$c?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$ca?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$cb?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$cc?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$cd?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$ce?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$cf?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$cg?></a></td> 
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$ch?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$ci?></a></td>
                                        <td><a href="PSTDataCDetail/<?=$uid?>/<?=$date?>/0/1"><?=$ot?></a></td>
                                    </tr>
                                    <?php }?>
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
      "responsive": false, "lengthChange": false, "autoWi$dth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

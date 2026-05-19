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
   
  <form class="p-3" method="POST" action="<?=base_url(); ?>/Menu/handoverDataReport">
    <input type="date" name="start_date" class="mr-2" value="<?=$sd ?>">
    <input type="date" name="end_date" class="mr-2" value="<?=$ed ?>">
    <button type="submit" class="bg-primary text-light">Search</button>
</form>
<section class="content">
<div class="text-right mt-2"><button class="btn btn-info" id="printPage">Print Page</button> <br><br>

      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
   
      <?php   
      if(isset($countHandover) && !empty($countHandover)){
        

      if(!empty($handoverDataReport)){ ?>
              <div class="card-header" style="text-align:left;">
                <h2 class="card-title"><b>Handover Summary Report</b></h2>
                <br>
        <ul><li>Total Handover: <?php echo $countHandover;?> 
            <li>Total CompaniesCovered: <?php echo $companyCount;?> 
             
        </ul>
              </div>
  
              <!-- /.card-header -->
               <?php 
               foreach($handoverDataReport as $useridKey=>$value){ 
                  $companyCount = $this->Menu_model->getTotalCompanyCount($useridKey,'client_name',$sd,$ed);
                 // $schoolCount  = $this->Menu_model->getTotalCompanyCount($useridKey,'noofschool');
                ?>
                <div class="summary-highlight" style="text-align:left">
        <h3>Handover Report for <?= getUserNameById($useridKey); ?></h3>
        <ul>
        <?php   
                echo "<li>Total Handover: ".count($value) ;
                echo "<li>Total CompaniesCovered: ".$companyCount ;
               // echo "<li>Total Schools : ".$schoolCount ;
                ?>
        </ul>
      
    </div>
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>

                            <div class="table-responsive">  
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead>
                                    <tr>
                                            <th>Sr. No.</th>
                                            <th>BD Name</th>
                                            <th>Handover Date</th>
                                            <th>Company Name</th>
                                            <th>Project Code</th>
                                            <th>Total Logs</th>
                                            <th>Total School</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                          $i=1;
                                          $bdid = $this->Menu_model->get_userbyaid($uid);
                                          $bdid = $bdid->user_id;
                                          if(isset($value)){                                          
                                        foreach($value as $dt=>$val){
                                            $cid         = $val['cid'];
                                            $val['logs'] = $this->Menu_model->get_handoverlog($cid);
                                            $lastlogs    = $this->Menu_model->get_lasthlog($cid);
                                        ?>
                                    <tr>
                                         <td><?=$i?></td>
                                         <td><?= getUserNameById($useridKey); ?></td>
                                         <td><?=  isset($val['sdatet']) ?   $val['sdatet'] : "--" ; ?></td>
                                         <td><?= $val['client_name']?></td>
                                         <td><?= $val['projectcode']?></td>
                                         <td><a href="programlogs/<?=$cid?>"><?=sizeof($val['logs'])?></a></td>
                                         <td><?= $val['noofschool']?></td>
                                         <td>Open</td>
                                     </tr>
                                     <?php $i++;
                                    }
                                  }
                                    ?>
                                  </tbody>
                                </table>
                </fieldset>
            </div>
            <hr />
        </div>
    </div></div>
    <?php }
    }
    else{
      ?>
      <div class="card-header">
                <h3 class="card-title">Handover Detail</h3>
      </div>
      <?php
    } ?>
</div>
<?php }
else{ ?>
<div class="card-header"><h3 class="card-title">No Handover Data Found</h3> </div>

<?php
}
?>
  </div></div>
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
<script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
        })
  </script>
</body>
</html>

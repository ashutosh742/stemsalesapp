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
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <button onclick="printPDF()" id="generate-pdf">Download</button>
            <div class="abc p-3 m-3" id="abc" style="font: 15px Arial, sans-serif;color:black;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <?php $users = $this->Menu_model->get_allactiveuserbyaid($uid);$tdate=date('Y-m-d');?>
                    <div class="card-header"><center><h5><b>Current Day Live Task (<?=$tdate?>)</b></h5></center></div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th>Name</th>
                                <th>Not Inisated</th>
                                <th>Inisated</th>
                                <th>09:00am to 11:00am</th>
                                <th>11:00am to 13:00pm</th>
                                <th>13:00pm to 15:00pm</th>
                                <th>15:00pm to 17:00pm</th>
                                <th>17:00pm to 19:00pm</th>
                                <th>19:00pm to 21:00pm</th>
                            </tr>
                            <?php foreach($users as $user){$username = $user->name;?>
                            <tr>
                                <td><?=$username?></td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </div>
            
          <div class="col-12">
              <?php 
                    $time1 = array('09:00:00', '11:00:00', '13:00:00','15:00:00','17:00:00','19:00:00');
                    $time2 = array('11:00:00', '13:00:00', '15:00:00','17:00:00','19:00:00','21:00:00');
                    for($i=0;$i<6;$i++){
                        $t1=$time1[$i];
                        $t2=$time2[$i];
                        $tt1 = date("h:i a", strtotime($t1));
                        $tt2 = date("h:i a", strtotime($t2)); ?>
              <div class="card">
                  <div class="card-header"><center><h5><b>Task Between <?=$tt1?> to <?=$tt2?> (<?=$tdate?>)</b></h5></center></div>
                  <div class="card-body">
                      <?php foreach($users as $user){
                          $username = $user->name;
                          $userid = $user->user_id;
                          $ttbytime = $this->Menu_model->get_allusertask($userid,$tdate,$t1,$t2); 
                          $ted = $this->Menu_model->get_alluserttbytimed($userid,$tdate,$t1,$t2);
                          ?>
                      <div class="row">
                          <div class="col-lg-12 col-sm p-3 card">
                                <b><?=$username?></b><br> 
                                Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) | Proposal(<?=$ted[0]->g?>)
                           </div>
                                      <?php 
                                          foreach($ttbytime as $tt){
                                          $taid = $tt->actiontype_id;
                                          $taid=$this->Menu_model->get_action($taid);
                                          $time = $tt->appointmentdatetime;
                                          $utime = $tt->updateddate;
                                          $itime = $tt->initiateddt;
                                          $nextCFID = $tt->nextCFID;
                                          $time = date('h:i a', strtotime($time));
                                          $utime = date('h:i a', strtotime($utime));
                                          $itime = date('h:i a', strtotime($itime));
                                          if($nextCFID=='0'){$color='bg-danger';$bcolor='border-danger';$itime='';$utime='';}
                                          if($nextCFID!='0'){$color='bg-success';$bcolor='border-success';}
                                          $txt="";
                                            $upsell_client= $tt->upsell_client;
                                            $focus_funnel= $tt->focus_funnel;
                                            $keycompany= $tt->keycompany;
                                            $pkclient= $tt->pkclient;
                                            $priorityc= $tt->priorityc;
                                            if($upsell_client=='yes'){$txt=$txt.'Upsell Client, ';}
                                            if($focus_funnel=='yes'){$txt=$txt.'Focus Funnel, ';}
                                            if($keycompany=='yes'){$txt=$txt.'Key Client, ';}
                                            if($pkclient=='yes'){$txt=$txt.'Positive Key Client, ';}
                                            if($priorityc=='yes'){$txt=$txt.'Priority Client, ';}
                                      ?>
                                  <div class="col-lg-2 col-sm p-1">
                                        <span class="flex card border <?=$bcolor?>">
                                            <div class="<?=$color?> p-2"><b>
                                            <?=$taid[0]->name?> (<?=$tt->ostatus?>)<br> 
                                            <?=$tt->compname?><br><?=$txt?></b></div>
                                            <div class="p-2"><b>
                                            Plan Time:- <?=$time?><br>
                                            Initiat Time:- <?=$itime?><br>
                                            Update Time:- <?=$utime?></b></div>
                                        </span>
                                   </div>
                                  <?php } ?>
                                  </div>
                      <hr>
                      <?php } ?>
                  </div>
              </div>
              
              <?php } ?>
              
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
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.3/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
<script>
document.getElementById('generate-pdf').addEventListener('click', function() {
  var element = document.getElementById('pdf-content');
  html2canvas(element).then(function(canvas) {
    var imgData = canvas.toDataURL('image/jpeg');
    var width = element.offsetWidth;
    var height = element.offsetHeight;
    var doc = new jsPDF({
      orientation: 'p',
      unit: 'px',
      format: [width, height]
    });
    doc.addImage(imgData, 'JPEG', 0, 0, width, height);
    doc.save('generated.pdf');
  });
});
</script>     
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
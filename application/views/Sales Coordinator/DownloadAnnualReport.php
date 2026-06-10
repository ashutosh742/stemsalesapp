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
  <?php require('addlog.php');?>
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
              <h4><?php $uid=$user['user_id']?></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
   <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-12 col-md-12 col-12 m-auto">
            <!-- Default box -->
            <div class="card card-primary">
              <div class="card-header">Annual Review of Last FY 2022-23</div>
              <!-- /.card-header -->
              <div class="card-body box-profile p-5">
                  <center><h3>BD Name: <?=$annualr1[0]->uname?></h3></center>
                            <div class="row">
                                <div class="col-12 p-2">
                                    <lable><h5>Meeting Detail</h5></lable>
                                    <table class="table">
                                        <tr>
                                            <th>Total RP Meeting</th>
                                            <th>CSR</th>
                                            <th>Govt</th>
                                            <th>NGO</th>
                                            <th>Private School</th>
                                            <th>Other</th>
                                        </tr>
                                        <tr>
                                            <td><?=$annualr1[0]->total?></td>
                                            <td><?=$annualr1[0]->csr?></td>
                                            <td><?=$annualr1[0]->govt?></td>
                                            <td><?=$annualr1[0]->ngo?></td>
                                            <td><?=$annualr1[0]->pschool?></td>
                                            <td><?=$annualr1[0]->other?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Districts Travelled</h5></lable>
                                    <?php 
                                        $district  = $annualr1[0]->noddistrict;
                                        $district = explode(',', $district);
                                        $l = sizeof($district);
                                    ?>
                                    <table class="table">
                                        <tr>
                                            <th>No of District</th>
                                            <th>Total Meeting</th>
                                            <th>RP Meeting</th>
                                            <th>Fresh Meeting</th>
                                            <th>Re-meeting</th>
                                        </tr>
                                        <tr>
                                            <td><?=$l?></td>
                                            <td><?=$annualr1[0]->totalmeeting?></td>
                                            <td><?=$annualr1[0]->rpmeeting?></td>
                                            <td><?=$annualr1[0]->freshmeeting?></td>
                                            <td><?=$annualr1[0]->remeeting?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <div class="row">.
                                        <?php for($i=0;$i<$l;$i++){ if($i>0){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$district[$i]?></b>
                                        </div>
                                        <?php }} ?>     
                                    </div>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Potential district/ Top district of respective region</h5>
                                    <?php 
                                        $pdistrict  = $annualr1[0]->pdistrict;
                                        $pdistrict = explode(',', $pdistrict);
                                        $l = sizeof($pdistrict);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){ if($i>0){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$pdistrict[$i]?></b>
                                        </div>
                                        <?php }} ?>     
                                    </div>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Aspirational district of your respective region</h5>
                                    <?php 
                                        $adistrict  = $annualr1[0]->adistrict;
                                        $adistrict = explode(',', $adistrict);
                                        $l = sizeof($adistrict);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){ if($i>0){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$adistrict[$i]?></b>
                                        </div>
                                        <?php }} ?>     
                                    </div>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Top spender companies</h5>
                                    <?php 
                                        $topspender  = $annualr1[0]->topspender;
                                        $topspender = explode(',', $topspender);
                                        $l = sizeof($topspender);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){ if($i>0){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$topspender[$i]?></b>
                                        </div>
                                        <?php }} ?>     
                                    </div>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Top Spender RP Companies Completed</h5>
                                    <?php 
                                        $topspenderrp  = $annualr1[0]->topspenderrp;
                                        $topspenderrp = explode(',', $topspenderrp);
                                        $l = sizeof($topspenderrp);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){ if($i>0){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$topspenderrp[$i]?></b>
                                        </div>
                                        <?php }} ?>     
                                    </div>
                                </div>
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Connect Detail</h5></lable>
                                    <table class="table">
                                        <tr>
                                            <th>LinkedIn Connect</th>
                                            <th>CSR Broadcast</th>
                                            <th>Private school Broadcast</th>
                                            <th>Govt Broadcast</th>
                                        </tr>
                                        <tr>
                                            <td><?=$annualr1[0]->linkedIn?></td>
                                            <td><?=$annualr1[0]->bcsr?></td>
                                            <td><?=$annualr1[0]->bpschool?></td>
                                            <td><?=$annualr1[0]->bgovt?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Current funnel report (in separate format)</h5></lable>
                                    <table class="table">
                                        <tr>
                                            <th>Total Companies</th>
                                            <th>Open</th>
                                            <th>Open [RPEM]</th>
                                            <th>Reachout</th>
                                            <th>Tentative</th>
                                            <th>Will-Do-Later</th>
                                            <th>Not-Interest</th>
                                            <th>TTD-Reachout</th>
                                            <th>WNO-Reachout</th>
                                        </tr>
                                        <tr>
                                            <td><?=$annualr2[0]->ftotal?></td>
                                            <td><?=$annualr2[0]->fopen?></td>
                                            <td><?=$annualr2[0]->frpem?></td>
                                            <td><?=$annualr2[0]->freachout?></td>
                                            <td><?=$annualr2[0]->ftentative?></td>
                                            <td><?=$annualr2[0]->fwdl?></td>
                                            <td><?=$annualr2[0]->fni?></td>
                                            <td><?=$annualr2[0]->fttd?></td>
                                            <td><?=$annualr2[0]->fwno?></td>
                                        </tr>
                                    </table>
                                    <table class="table">
                                        <tr>
                                            <th>Positive</th>
                                            <th>Very Positive</th>
                                            <th>Positive NAP</th>
                                            <th>Very Positive NAP</th>
                                            <th>Focus Funnel</th>
                                            <th>Upsell Client</th>
                                            <th>Closure</th>
                                            <th>Key Client</th>
                                        </tr>
                                        <tr>
                                            <td><?=$annualr2[0]->fpositive?></td>
                                            <td><?=$annualr2[0]->fvpositive?></td>
                                            <td><?=$annualr2[0]->fpnap?></td>
                                            <td><?=$annualr2[0]->fvpnap?></td>
                                            <td><?=$annualr2[0]->fffunnel?></td>
                                            <td><?=$annualr2[0]->fupsellclient?></td>
                                            <td><?=$annualr2[0]->fclosure?></td>
                                            <td><?=$annualr2[0]->fkeyclient?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Model school visit/ readyness</h5>
                                    <?php 
                                        $vschool  = $annualr3[0]->vschool;
                                        $vdistrict  = $annualr3[0]->vdistrict;
                                        
                                        
                                        $vschool = explode(',', $vschool);
                                        $vdistrict = explode(',', $vdistrict);
                                        $l = sizeof($vschool);
                                        $m = sizeof($vdistrict);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){  ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$vschool[$i]?> (<?php if($i<$m){echo $vdistrict[$i];} ?>)</b>
                                        </div>
                                        <?php } ?>     
                                    </div>
                                </div>
                                
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Client visit to model school</h5>
                                    <?php 
                                        $cvclient  = $annualr3[0]->cvclient;
                                        $cvschool  = $annualr3[0]->cvschool;
                                        $cvdistrict  = $annualr3[0]->cvdistrict;
                                        
                                        
                                        $cvclient = explode(',', $cvclient);
                                        $cvschool = explode(',', $cvschool);
                                        $cvdistrict = explode(',', $cvdistrict);
                                        
                                        $l = sizeof($cvclient);
                                        $m = sizeof($cvschool);
                                        $n = sizeof($cvdistrict);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){  ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$cvclient[$i]?> (<?php if($i<$m){echo $cvschool[$i];} ?>) (<?php if($i<$n){echo $cvdistrict[$i];} ?>)</b>
                                        </div>
                                        <?php } ?>     
                                    </div>
                                </div>
                                
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Closure Done</h5>
                                    <?php 
                                        $cdcompany  = $annualr3[0]->cdcompany;
                                        $cdschool  = $annualr3[0]->cdschool;
                                        $cdrevenue  = $annualr3[0]->cdrevenue;
                                        
                                        
                                        $cdcompany = explode(',', $cdcompany);
                                        $cdschool = explode(',', $cdschool);
                                        $cdrevenue = explode(',', $cdrevenue);
                                        
                                        $l = sizeof($cdcompany);
                                        $m = sizeof($cdschool);
                                        $n = sizeof($cdrevenue);
                                    ?>
                                    <div class="row">
                                        <?php $school=0;$revenue=0; for($i=0;$i<$l;$i++){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$cdcompany[$i]?> (<?php if($i<$m){echo $cdschool[$i];$school=$school+$cdschool[$i];} ?>) (<?php if($i<$n){echo $cdrevenue[$i];$revenue=$revenue+$cdrevenue[$i];} ?>)</b>
                                        </div>
                                        <?php } ?> 
                                        
                                        <b>Total School : <?=$school?></b><br>
                                        <b>Total Revenue : <?=$revenue?></b><br>
                                    </div>
                                </div>
                                
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Active Key client for FY 2023-24</h5>
                                    <?php 
                                        $kccompanies  = $annualr3[0]->kccompanies;
                                        $kcschool  = $annualr3[0]->kcschool;
                                        $kcrevenue  = $annualr3[0]->kcrevenue;
                                        
                                        
                                        $kccompanies = explode(',', $kccompanies);
                                        $kcschool = explode(',', $kcschool);
                                        $kcrevenue = explode(',', $kcrevenue);
                                        
                                        $l = sizeof($kccompanies);
                                        $m = sizeof($kcschool);
                                        $n = sizeof($kcrevenue);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$kccompanies[$i]?> (<?php if($i<$m){echo $kcschool[$i];} ?>) (<?php if($i<$n){echo $kcrevenue[$i];} ?>)</b>
                                        </div>
                                        <?php } ?>     
                                    </div>
                                </div>
                                
                                
                                <div class="col-12 p-2">
                                    <lable><h5>Active Key client for FY 2023-24 (Q1)</h5>
                                    <?php 
                                        $kccompaniesq  = $annualr3[0]->kccompaniesq;
                                        $kcschoolq  = $annualr3[0]->kcschoolq;
                                        $kcrevenueq  = $annualr3[0]->kcrevenueq;
                                        
                                        
                                        $kccompaniesq = explode(',', $kccompaniesq);
                                        $kcschoolq = explode(',', $kcschoolq);
                                        $kcrevenueq = explode(',', $kcrevenueq);
                                        
                                        $l = sizeof($kccompaniesq);
                                        $m = sizeof($kcschoolq);
                                        $n = sizeof($kcrevenueq);
                                    ?>
                                    <div class="row">
                                        <?php for($i=0;$i<$l;$i++){ ?>
                                        <div class="col-3 p-1">
                                            <b><?=$i?>. <?=$kccompaniesq[$i]?> (<?php if($i<$m){echo $kcschoolq[$i];} ?>) (<?php if($i<$n){echo $kcrevenueq[$i];} ?>)</b>
                                        </div>
                                        <?php } ?>     
                                    </div>
                                </div>
                                
                                 <img src="<?=base_url();?><?=$annualr3[0]->filea?>">
                                <img src="<?=base_url();?><?=$annualr3[0]->fileb?>">
                                <img src="<?=base_url();?><?=$annualr3[0]->filec?>">
                                <img src="<?=base_url();?><?=$annualr3[0]->filed?>">
                                
                                
                            <lable><h5>Achievement in the last FY:</h5><?=$annualr3[0]->Achievement?></lable>
                        </div>
                    </div>
            </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>

function addsvisit() {
      var schoolvisit = document.getElementById("schoolvisit");
      var svdata = document.getElementById("svdata");
      schoolvisit.appendChild(svdata.cloneNode(true));
}

function addcsvisit() {
    
      var cschoolvisit = document.getElementById("cschoolvisit");
      var csvdata = document.getElementById("csvdata");
      cschoolvisit.appendChild(csvdata.cloneNode(true));
}


function kcclick() {
    
      var kc = document.getElementById("kc");
      var kcdata = document.getElementById("kcdata");
      kc.appendChild(kcdata.cloneNode(true));
}


function kcq1click() {
    
      var kcq1 = document.getElementById("kcq1");
      var kcq1data = document.getElementById("kcq1data");
      kcq1.appendChild(kcq1data.cloneNode(true));
}








$('#currentStatus').on('change', function b() {
var sid = document.getElementById("currentStatus").value;
$.ajax({
url:'<?=base_url();?>Menu/getremark',
type: "POST",
data: {
sid: sid
},
cache: false,
success: function a(result){
$("#remarks").html(result);
}
});
});

$('#action_type').on('change', function b() {
var aid = document.getElementById("action_type").value;
var sid = document.getElementById("currentStatus").value;
$.ajax({
url:'<?=base_url();?>Menu/getpurpose',
type: "POST",
data: {
aid: aid,
sid: sid
},
cache: false,
success: function a(result){
$("#purpose").html(result);
}
});
});


$('#purpose').on('change', function b() {
var pid = document.getElementById("purpose").value;
$.ajax({
url:'<?=base_url();?>Menu/getnextaction',
type: "POST",
data: {
pid: pid
},
cache: false,
success: function a(result){
$("#next_action").html(result);
}
});
});


$('#id_state').on('change', function b() {
var stid = document.getElementById("id_state").value;
$.ajax({
url:'<?=base_url();?>Menu/getcitybystate',
type: "POST",
data: {
stid: stid
},
cache: false,
success: function a(result){
$("#city").html(result);
}
});
});
function replaceBudget(){                    
    var budgetdiv= document.getElementById('budgetdiv');
    var id_partnerType= document.getElementById('id_partnerType').value;
    if(id_partnerType=="4"){
    budgetdiv.innerHTML='<lable>Category</lable><br><select id="budget" class="form-control" name="budget" required><option>A</option><option>B</option><option>C</option></select>';    
    }
    alert('budget checking '+id_partnerType);
}
var id_partnerType=document.getElementById('id_partnerType');
id_partnerType.addEventListener("change", replaceBudget);


</script>
          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
    </div></div></div>
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
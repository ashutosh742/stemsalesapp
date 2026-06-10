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
          <div class="col-lg-6 col-md-6 col-12 m-auto">
            <!-- Default box -->
            <div class="card card-primary">
              <div class="card-header">Next Day Planner</div>
              <!-- /.card-header -->
              <div class="card-body box-profile p-5">
                <!-- form start -->
                        <?php 
                            date_default_timezone_set("Asia/Kolkata");
                            $nextdate = date('Y-m-d', strtotime('+1 day')); 
                            $nxtdtask=$this->Menu_model->get_nxtdtask($uid,$nextdate);
                            $nxtdplan = $this->Menu_model->get_nxtdplan($uid,$nextdate);
                            $nxtdrev=$this->Menu_model->get_nxtdreviewplan($uid,$nextdate);
                            $rcont=$nxtdrev[0]->cont;
                         ?>
                        
                        <?php foreach($nxtdtask as $ndt){
                            $totalt = $ndt->a*15+$ndt->b*10+$ndt->c*60+$ndt->d*60+$ndt->e*5+$ndt->f*5+$ndt->g*15+$ndt->j*10+$ndt->k*30+$rcont*60;
                        ?>
                        <span class="badge p-2 m-1 bg-info" >Total Task&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->ab?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Call&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->a?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Email&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->b?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Scheduled Meeting&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->c?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Barg in Meeting&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->d?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Whatsapp Activity&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->e?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Write MOM&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->f?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Write Proposal&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->g?></span></span>
                        <span class="badge p-2 m-1 bg-info" >Research&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->j?></span></span>
                        <span class="badge p-2 m-1 bg-info" >documentation&nbsp;&nbsp;<span class="badge bg-light"><?=$ndt->l?></span></span>
                        <?php }?>
                        
                        
                        <?php ?>
                        <span class="badge p-2 m-1 bg-info" >Review&nbsp;&nbsp;<span class="badge bg-light"><?=$rcont?></span></span>
                        
                        <?php ?>
                        
                        <?php 
                        if($totalt==0){$color='alert-danger';$msg='Could you please plan a task for tomorrow?';}
                        elseif($totalt<240){$color='alert-warning';$msg='Could you please plan some additional tasks for tomorrow?';}
                        elseif($totalt>480){$color='alert-danger';$msg='Are you sure you will be able to do these tasks tomorrow?';}
                        else{$color='alert-success';$msg='Good..';}
                        ?>
                        
            <?php if($nxtdplan[0]->cont<=0){?>  
            
            <div class="alert <?=$color?>" role="alert"><?=$msg?></div>
            <form method="post" action="<?=base_url();?>Menu/ndplan">
                <input type="hidden" value="<?=$uid?>" name="bdid"> 
                <div class="was-validated">
                        <div class="col-md-12 col-sm mt-3">
                            <lable>Plann for</lable>
                            <input class="form-control" type="date" value="<?=$nextdate?>" name="nextdate" readonly>
                        </div>
                        
                        <div class="col-md-12 col-sm mt-3">
                            <select class="form-control" name="wffo" required="">
                                <option value="">Please Select Your Next Day Plan</option>
                                <option value="1">Work From Office</option>
                                <option value="2">Work From Field</option>
                                <option value="3">Work From Field+Office</option>
                            </select>
                            <div class="invalid-feedback">Please provide a Company name.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <div class="col-md-12 col-sm mt-3">
                            <textarea class="form-control" name="reminder" required=""></textarea>
                            <div class="invalid-feedback">Please provide a Company name.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    <?php if($totalt>=240){?><button class="btn btn-primary" type="submit">Submit</button><?php } ?>
                </div>
            </form>
            <br>
            
            <?php } else {?>
            <div><br><h5>Thanks... You Are Planned for Next Day.</h5></div>
            <?php }?>
            
        
        
        
              
              
            </div>
            <!-- /.card -->
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
    budgetdiv.innerHTML='<label for="validationSample01">Category</label><select id="budget" class="form-control" name="budget" required><option>A</option><option>B</option><option>C</option></select>';    
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM Operation | WebAPP</title>

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
    <!-- /.content-header -->
<!-- Main content -->
<?php if($do==1){?>
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Daily Team Coordination</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/dailytc" method="post">
                    <div class="form-group">
                        <?php date_default_timezone_set("Asia/Kolkata"); 
                        $bdid=$this->Menu_model->get_userbyaid('45');
                        ?>
                        <input type="datetime" name="start" class="form-control" value="<?=date('Y-m-d H:i:s')?>" readonly>
                        <input type="hidden" name="piid" class="form-control" value="<?=$uid?>">
                        <select class="form-control" name="bdid">
                            <?php foreach($bdid as $bdi){?>
                                <option value="<?=$bdi->user_id?>"><?=$bdi->name?></option>
                            <?php } ?>
                        </select>
                        <textarea class="form-control" name="pc" placeholder="Preparation Check"></textarea>
                        <textarea class="form-control" name="dsr" placeholder="Documents Support Required"></textarea>
                        <textarea class="form-control" name="apps" placeholder="App Support"></textarea>
                        <textarea class="form-control" name="other" placeholder="Others (if any)"></textarea>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Start Coordination</button>
                    </div>
                  </form>
            </div>
          </div>
      </div>   
     </div>     
    </section>
    <?php } if($do==2){?>
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Manage Your Day</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/dailytcc" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="datetime" name="close" class="form-control" value="<?=date('Y-m-d H:i:s')?>" readonly>
                        <input type="hidden" name="piida" class="form-control" value="<?=$uid?>">
                        <textarea class="form-control" name="cremark" placeholder="Close Remark" required></textarea>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Close Coordination</button>
                    </div>
                  </form>
            </div>
          </div>
      </div>   
     </div>     
    </section>
  <?php } ?>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>



document.getElementById("location").style.display = "none";
imgInp.onchange = evt => {
  const [file] = imgInp.files
  if (file) {
    blah.src = URL.createObjectURL(file);
    document.getElementById("location").style.display = "block";
    
    
  }
}


var x = document.getElementById("lat");
var y = document.getElementById("lng");
var z = document.getElementById("mylocation");
$(document).ready(function(){
    if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    x.value = "Geolocation is not supported by this browser.";
  }
});
function showPosition(position) {
  x.value = position.coords.latitude; 
  y.value = position.coords.longitude;
  var a = position.coords.latitude;
  var b = position.coords.longitude;
  mylocation.src = "https://maps.google.com/?q="+a+","+b+"&t=k&z=13&ie=UTF8&iwloc=&output=embed";
}




$('#task_subtype').on('change', function() {
   var tst = this.value;
   var tt = document.getElementById("task_type").value;
   if(tt=="VISIT"){
       if(tst=="New Client"){
          $("#box1").show();
          $("#box2").hide(); 
       }
       if(tst=="Onboard Client" || tst=="Inauguration"){
          $("#box2").show();
          $("#box1").hide();
       }
   }
   if(tt=="TTP"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="M&E"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="DIY"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="Utilisation"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="Call"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="Email"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="Whatsapp"){
      $("#box2").show();
      $("#box1").hide();
   }
   if(tt=="Other"){
      $("#box2").hide();
      $("#box1").hide();
   }
});

$('#region').on('change', function b() {
var dep = document.getElementById("dep").value;
var region = document.getElementById("region").value;
   
$.ajax({
url:'<?=base_url();?>Menu/getuserbydr',
type: "POST",
data: {
dep: dep,
region: region
},
cache: false,
success: function a(result){
$("#to_user").html(result);
}
});
});


$('#task_type').on('change', function c() {
var tt = document.getElementById("task_type").value;
$.ajax({
url:'<?=base_url();?>Menu/getpitst',
type: "POST",
data: {
tt: tt
},
cache: false,
success: function a(result){
$("#task_subtype").html(result);
}
});
});

$('#pcode').on('change', function b() {
var pcode = document.getElementById("pcode").value;
$.ajax({
url:'<?=base_url();?>Menu/getspdbypcs',
type: "POST",
data: {
pcode: pcode
},
cache: false,
success: function a(result){
$("#spd_id").html(result);
}
});
});

$('#pcode').on('change', function b() {
var pcode = document.getElementById("pcode").value;
$.ajax({
url:'<?=base_url();?>Menu/getspdbypcs',
type: "POST",
data: {
pcode: pcode
},
cache: false,
success: function a(result){
$("#spd_id1").html(result);
}
});
});

</script>

          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- jquery-validation -->
<script src="<?=base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?=base_url();?>assets/js/additional-methods.min.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
$(function() {
  $.validator.setDefaults({
    submitHandler: function () {
      alert( "Form successful submitted!" );
    }
  });
});
</script>
</body>
</html>
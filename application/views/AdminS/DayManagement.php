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
<?php if($do==0){?>
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Manage Your Day</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/daysc" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" name="user_id" value="<?=$uid?>">
                        <center><b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <input type="hidden" name="ustart" value="<?=date('Y-d-m H:i:s')?>">
                        <p>You Are Starting Day at <b><?=date('H:i:s');?></b><br><br>
                        <div class="mb-4">
                            <select class="form-control" name="wffo">
                                <option value="1">Work From Office</option>
                                <option value="2">Work From Field</option>
                                <option value="3">Work From Field+Office</option>
                            </select>
                        </div>
                        <div class="mb-4 d-flex justify-content-center">
                            <img class="border" id="blah" src="https://mdbootstrap.com/img/Photos/Others/placeholder.jpg" alt="your image" style="width:150px;height:250px"/>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="btn btn-info btn-rounded">
                                <label class="form-label text-white m-1" for="imgInp">Take Selfie</label>
                                <input type="file" class="form-control d-none" id="imgInp" name="filname" accept="image/*" capture required/>
                            </div>
                        </div>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                        <input type="hidden" name="do" value="<?=$do?>">
                    </div>
                    <div id="location">
                        <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                          <iframe style="width:100%;height:200px;" id="mylocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Start Your Day</button>
                    </div>
                  </form>
            </div>
          </div>
      </div>   
     </div>     
    </section>
    <?php } if($do==1){?>
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Manage Your Day</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/daysc" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="hidden" name="user_id" value="<?=$uid?>">
                        <center><b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
                        <p>You Are Started Day at <b><?=$mdata[0]->ustart?></b></p>
                        <p>You Are Closeing Day at <b><?=date('H:i:s');?></b></p>
                        <div class="mb-4 d-flex justify-content-center">
                            <img class="border" id="blah" src="https://mdbootstrap.com/img/Photos/Others/placeholder.jpg" alt="your image" style="width:150px;height:250px"/>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="btn btn-info btn-rounded">
                                <label class="form-label text-white m-1" for="imgInp">Take Selfie</label>
                                <input type="file" class="form-control d-none" id="imgInp" name="filname" accept="image/*" capture required/>
                            </div>
                        </div>
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">
                        <input type="hidden" name="do" value="<?=$do?>">
                    </div>
                    <div id="location">
                        <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                          <iframe style="width:100%;height:200px;" id="mylocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-danger" onclick="this.form.submit(); this.disabled = true;">Close Your Day</button>
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
                    <div class="form-group">
                        <input type="hidden" name="user_id" value="<?=$uid?>">
                        <center><b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
                        <p>You Are Started Day at <b><?=$mdata[0]->ustart?></b></p>
                        <p>You Are Closed Day at <b><?=$mdata[0]->uclose?></b></p>
                    </div>
            </div>
          </div>
      </div>   
     </div>     
    </section>
  <?php }?>
  
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
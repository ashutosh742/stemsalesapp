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
<style>


.circle-image img{

	border: 6px solid #fff;
    border-radius: 100%;
    padding: 0px;
    top: -28px;
    position: relative;
    width: 70px;
    height: 70px;
    border-radius: 100%;
    z-index: 1;
    background: #e7d184;
    cursor: pointer;

}


.dot {
      height: 18px;
    width: 18px;
    background-color: blue;
    border-radius: 50%;
    display: inline-block;
    position: relative;
    border: 3px solid #fff;
    top: -48px;
    left: 186px;
    z-index: 1000;
}

.name{
	margin-top: -21px;
	font-size: 18px;
}


.fw-500{
	font-weight: 500 !important;
}


.start{

	color: green;
}

.stop{
	color: red;
}


.rate{

	border-bottom-right-radius: 12px;
	border-bottom-left-radius: 12px;

}



.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center
}

.rating>input {
    display: none
}

.rating>label {
    position: relative;
    width: 1em;
    font-size: 30px;
    font-weight: 300;
    color: #FFD600;
    cursor: pointer
}

.rating>label::before {
    content: "\2605";
    position: absolute;
    opacity: 0
}

.rating>label:hover:before,
.rating>label:hover~label:before {
    opacity: 1 !important
}

.rating>input:checked~label:before {
    opacity: 1
}

.rating:hover>input:checked~label:before {
    opacity: 0.4
}


.buttons{
	top: 36px;
    position: relative;
}


.rating-submit{
	border-radius: 15px;
	color: #fff;
	    height: 49px;
}


.rating-submit:hover{
	
	color: #fff;
}
</style>

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
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Check Day Start</h3>
                  <hr>
                  <?php 
                  
                  $tdate=date('Y-m-d');
                  $tttype="Day-Start-Review";
                  $start = $this->Menu_model->get_taskstart($uid,$tttype,$tdate);
                  if($start){$rid = $start[0]->id;
                  
                  if($mdata){?>
                  <form action="<?=base_url();?>Menu/checkdays" method="post">
                     <input type="hidden" name="udid" value="<?=$mdata[0]->udid?>">
                    <div class="was-validated">
                    <div class="form-group">
                        <?php foreach ($mdata as $md){ ?>
                            <div class="text-center"><h5><?=$md->name?> Started Their Day @<?=$md->ustart?></h5></div>
                            <div class="text-center"><h5>Today Working From <?php if($md->wffo=='1'){echo 'Office';}elseif($md->wffo=='2'){echo 'Field';}else{echo 'Field+Office';}?></h5></div>
                            <br>
                            <div class="text-center"><img src="<?=base_url();?><?=$md->usimg?>" class="img-thumbnail" width="50%"></div>
                            <br>
                            <div class="img-thumbnail" style="height: 300px"><iframe width="100%"  height="100%" src="https://maps.google.com/?q=<?=$md->slatitude?>,<?=$md->slongitude?>&t=k&z=13&ie=UTF8&iwloc=&output=embed"></iframe></div>
                            <br>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Started at Good Time">Started at Good Time</h5>
                                <div class="rating">
                                    <input type="radio" name="rat1" value="5" id="5"><label for="5">☆</label>
                                    <input type="radio" name="rat1" value="4" id="4"><label for="4">☆</label>
                                    <input type="radio" name="rat1" value="3" id="3"><label for="3">☆</label>
                                    <input type="radio" name="rat1" value="2" id="2"><label for="2">☆</label>
                                    <input type="radio" name="rat1" value="1" id="1"><label for="1">☆</label>
                                </div>
                                
                            </div>
                            
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Today Working as per Plan">Today Working as per Plan</h5>
                                <div class="rating">
                                    <input type="radio" name="rat2" value="5" id="10"><label for="10">☆</label>
                                    <input type="radio" name="rat2" value="4" id="9"><label for="9">☆</label>
                                    <input type="radio" name="rat2" value="3" id="8"><label for="8">☆</label>
                                    <input type="radio" name="rat2" value="2" id="7"><label for="7">☆</label>
                                    <input type="radio" name="rat2" value="1" id="6"><label for="6">☆</label>
                                </div>
                            </div>
                            
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Day Start Image is Good">Day Start Image is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat3" value="5" id="11"><label for="11">☆</label>
                                    <input type="radio" name="rat3" value="4" id="12"><label for="12">☆</label>
                                    <input type="radio" name="rat3" value="3" id="13"><label for="13">☆</label>
                                    <input type="radio" name="rat3" value="2" id="15"><label for="14">☆</label>
                                    <input type="radio" name="rat3" value="1" id="14"><label for="15">☆</label>
                                </div>
                            </div>
                            
                            
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Day Start Location as per Plan">Day Start Location as per Plan</h5>
                                <div class="rating">
                                    <input type="radio" name="rat4" value="5" id="16"><label for="16">☆</label>
                                    <input type="radio" name="rat4" value="4" id="17"><label for="17">☆</label>
                                    <input type="radio" name="rat4" value="3" id="18"><label for="18">☆</label>
                                    <input type="radio" name="rat4" value="2" id="19"><label for="19">☆</label>
                                    <input type="radio" name="rat4" value="1" id="20"><label for="20">☆</label>
                                </div>
                            </div>
                            
                            <textarea class="form-control" name="sremark" placeholder="Remark" required=""></textarea>
                            
                        <?php } ?>
                            <center><input type="submit" class="btn btn-success mt-3"></center>
                        </div>
                        
                        </div>
                  </form>
                  <a href="closedayreview/<?=$rid?>"><center><button type="button" class="btn btn-info">Close Day Start Review</button></center></a>
                  <?php }} else{?>
                  <a href="startdayreview/<?=$uid?>/<?=$tttype?>"><center><button type="button" class="btn btn-info">Start Day Start Review</button></center></a>
                  <?php }?>
            </div>
          </div>
      </div>   
     </div>     
    </section>


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
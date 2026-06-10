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
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           <?php 
           
           $revst = $this->Menu_model->get_reviewstarted($uid);
           if($revst){$bdid = $revst[0]->bdid;}else{$bdid=0;}
           if($bdid==0){
           ?>
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Plan Review/Calling Task</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/planreview" method="post">
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" name="uid" value="<?=$uid?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <lable>Review Start Date</lable>
                        <input type="datetime-local" name="plandate" value="<?=date('Y-m-d H:i:s')?>" min="<?=date('Y-m-d H:i:s')?>" class="form-control" required="">
                        
                        <div class="invalid-feedback">Please provide Plan Date Time.</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="mt-4">
                            <select class="form-control" name="reviewtype" required="" id="reviewtype">
                                <option value="Roaster">Roaster</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Fortnightly">Fortnightly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Querterly">Querterly</option>
                                <option value="PST Self Review">PST Self Review</option>
                            </select>
                        </div>
                        
                        <input type="checkbox" id="myCheckbox" onclick="myFunction()">
                        <label>Do You Want to Change Period Time Frame.</label><br>
                        
                        <lable>Review Period</lable>
                        <input type="date" name="fixdate" id="fixdate" value="<?=date('Y-m-d')?>" min="2023-04-01" class="form-control" required="" readOnly>
                        
                        <div class="mt-4">
                            <select class="form-control" name="bdid" required="">
                                <option value="<?=$uid?>"><?=$user['name']?></option>
                                <?php $bd = $this->Menu_model->get_userbyaid('45');
                                 foreach($bd as $bd){?>
                                 <option value="<?=$bd->user_id?>"><?=$bd->name?></option>
                                 <?php } ?>
                            </select>
                        </div>
                        <div class="mt-4">
                            <input type="text" name="meetlink" placeholder="Meeting Link" class="form-control" required="">
                            <div class="invalid-feedback">Please provide Meeting LInk.</div>
                        <div class="valid-feedback">Looks good!</div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Create Plan</button>
                    </div>
                    </div>
                  </form>
            </div>
          </div>
      </div> 
      
      <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Start Review/Calling Task</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/startreview" method="post">
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" name="uida" value="<?=$uid?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <input type="datetime-local" name="startt" value="<?=date('Y-m-d H:i:s')?>" class="form-control" readonly>
                        <div class="invalid-feedback">Please provide Start Date Time.</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="mt-4">
                            <select class="form-control" name="reviewid" required="">
                                <?php $reviewid = $this->Menu_model->get_reviewid($uid);
                                foreach($reviewid as $rev){
                                ?>
                                <option value="<?=$rev->rid?>"><?=$rev->name?> (<?=$rev->reviewtype?>) (<?=$rev->plant?>)</option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback">Please Create Plan First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Start Review</button>
                    </div>
                  </form>
            </div>
          </div>
      </div>
     </div>  
     <?php }else{ ?>
     <div class="col-sm col-md-6 col-lg-6 m-auto">
         <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h4 class="text-center"><?=$revst[0]->reviewtype;?> Review/Calling</h4>
                  <h5 class="text-center"><?=$revst[0]->name;?></h5>
                  <hr>
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" name="uidaa" value="<?=$uid?>">
                        <input type="hidden" id="revtype" value="<?=$revst[0]->reviewtype;?>">
                        <input type="hidden" id="bdid" value="<?=$revst[0]->bdid;?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <input type="date" id="fdate" name="fdate" value="<?=$revst[0]->fixdate;?>" class="form-control" readonly>
                        <input type="hidden" id="pstid" value="<?=$uid?>">
                        <div class="invalid-feedback">Please provide Start Date Time.</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="mt-4">
                            <select class="form-control" id="categories" name="categories" required="">
                                <option value="0">All</option>
                                <option value="keycompany">Key Client</option>
                                <option value="pkclient">Positive Key Client</option>
                                <option value="focus_funnel">Focus Funnel</option>
                                <option value="topspender">Top Spender</option>
                                <option value="Review">Review</option>
                            </select>
                            <div class="invalid-feedback">Please Create Plan First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <div class="mt-4">
                            <select class="form-control" id="statusid" name="statusid" required="">
                                <option value="">Select Status</option>
                                <option value="0">All</option>
                                <?php $status = $this->Menu_model->get_status($uid);
                                foreach($status as $st){
                                ?>
                                <option value="<?=$st->id?>"><?=$st->name?></option>
                                <?php } ?>
                            </select>
                            <div class="invalid-feedback">Please Create Plan First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <div class="mt-4">
                            <select class="form-control" name="inid" required="" id="inid">
                            </select>
                            <div class="invalid-feedback">Please Select Status First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    </div>
                    <a href="<?=base_url();?>Menu/AllReviewac"><button type="button" class="btn btn-outline-danger">Go to Action wise Review</button></a><br>
              </div>
              
              <div class="card p-3 mt-3">
                  <div class="was-validated">
                  <form action="<?=base_url();?>Menu/closereview" method="post">
                      <input type="hidden" name="rrid" value="<?=$revst[0]->rid;?>">
                      <b class="text-danger mt-3">Are you sure you want to close review task ?</b>
                      <div class="form-group">
                        <textarea class="form-control mt-3" name="closeremark" placeholder="Review Close Final Remark..."  required=""></textarea>
                        <input type="datetime-local" name="closetdate" value="<?=date('Y-m-d H:i:s');?>" class="form-control mt-3" required="">
                        <div class="form-group text-center mt-3">
                            <button type="submit" class="btn btn-danger" onclick="this.form.submit(); this.disabled = true;">Close Review</button>
                        </div>
                    </div>
                  </form>
                  </div>
              </div>
              
              
             
         </div>
     </div></div>
     <div class="col-sm col-md-6 col-lg-6 m-auto">
         <div class="card card-primary card-outline">
             <form action="<?=base_url();?>Menu/pstrtaskc" method="post">
                 <input type="hidden" id="rtype" name="rtype" value="<?=$revst[0]->reviewtype;?>">
              <div class="card-body box-profile" id="cmpdata">
              </div>
                  <div class="was-validated m-3">
                    <div class="form-group">
                        <textarea class="form-control" name="remark" placeholder="Review Remark..."  required=""></textarea>
                    </div>
                    
                    <hr>
                       <input type="hidden" name="pstuid" value="<?=$uid?>">
                       <input type="hidden" name="bduid" value="<?=$bdid?>">
                       <input type="hidden" name="rid" value="<?=$revst[0]->rid;?>">
                       <select id="taskupdate" name="taskupdate" class="form-control">
                           <option value="">Select Task Update</option>
                           <option>Completed</option>
                           <option>Planned but Not Completed</option>
                           <option>Not Planned</option>
                       </select>
                       <div id="rosterhide">
                           <hr>
                       <input type="hidden" name="pstuid" value="<?=$uid?>">
                       <input type="hidden" name="bduid" value="<?=$bdid?>">
                       <input type="hidden" name="rid" value="<?=$revst[0]->rid;?>">
                       
                       
                       <div id="orrr">
                           <lable>Potential / Non-Potential Clients? </lable>
                           <select class="form-control" id="potential" name="potential">
                               <option value='yes'>Potential</option>
                               <option value='no'>Non-Potential</option>
                           </select>
                           
                           <lable>1st Remark</lable>
                           <select class="form-control" id="otherremark">
                               <option value="">Select Remark</option>
                           </select>
                           <input type="text" class="form-control" name="otherremark" id="otherremark1">
                           
                           <lable>Call Remark</lable>
                           <select class="form-control" id="ans1">
                               <option value="">Select Remark</option>
                           </select>
                           <input type="text" class="form-control" name="ans1" id="ans11">
                           
                           <lable>Email Remark</lable>
                           <select class="form-control" id="ans2">
                               <option value="">Select Remark</option>
                           </select>
                           <input type="text" class="form-control" name="ans2" id="ans21">
                           
                           <lable>Meeting Remark</lable>
                           <select class="form-control" id="ans3">
                               <option value="">Select Remark</option>
                           </select>
                           <input type="text" class="form-control" name="ans3" id="ans31">
                       
                       </div>
                       
                       
                       <lable>Is the Current Status right? </lable>
                       <select class="form-control" id="statusright" name="statusright">
                           <option>Yes</option>
                           <option>No</option>
                       </select>
                       
                       <div id="statusno">
                            <select class="form-control" name="requeststatus" required="">
                                <?php $status = $this->Menu_model->get_status($uid);
                                foreach($status as $st){?>
                                <option value="<?=$st->id?>"><?=$st->name?></option>
                                <?php } ?>
                            </select>
                           <textarea name="ans4" class="form-control" placeholder="Remark...."></textarea>
                       </div>
                       
                       <lable>Need to Delete This Company From Your Funnel? </lable>
                       <input type="text" class="form-control" name="deletef" value="No">
                       
                       <lable>Need to Change Partner Type? </lable>
                       <select class="form-control" name="patnertype">
                           <option>No</option>
                       </select>

                       <lable>Top Spender? </lable>
                       <select class="form-control" name="topspender">
                           <option>No</option>
                           <option>Yes</option>
                       </select>
                       
                       <lable>Key Client? </lable>
                       <select class="form-control" name="keyclient">
                           <option>No</option>
                           <option>Yes</option>
                       </select>
                       
                       <lable>Positive Key Client? </lable>
                       <select class="form-control" name="pkeyclient">
                           <option>No</option>
                           <option>Yes</option>
                       </select>
                       
                       <lable>Priority Client? </lable>
                       <select class="form-control" name="priorityclient">
                           <option>No</option>
                           <option>Yes</option>
                       </select>
                       
                       <lable>Upsell Client? </lable>
                       <select class="form-control" name="upsellclient">
                           <option>No</option>
                           <option>Yes</option>
                       </select>
                       
                       <lable>Focus Funnel? </lable>
                       <select class="form-control" name="focusyclient">
                           <option>No</option>
                           <option>Yes</option>
                       </select>
                       
                       <center><h5>Create Task</h5></center>
                       <input type="datetime-local" id="ntdate" name="ntdate" value="<?=date('Y-m-d H:i:s');?>" class="form-control" required="">
                       <lable>Task Assign For</lable>
                       <select id="taskfor" name="taskfor" class="form-control"  required="">
                           <option value="0">RM</option>
                           <option value="1">BD</option>
                       </select>
                       <lable>Select Action</lable>
                       <select id="ntaction" name="ntaction" class="form-control"  required="">
                           <option value="">Select Action</option>
                           <?php $action = $this->Menu_model->get_action();
                           foreach($action as $a){?>
                               <option value="<?=$a->id?>"><?=$a->name?></option>
                           <?php } ?>
                       </select>
                       <lable>Select Purpose</lable>
                       <select id="ntppose" class="form-control" name="ntppose" required="">
                       </select>
                   
                   <lable>Expected Status</lable>
                   <select class="form-control" id="exsid" name="exsid" required="">
                        <option value="">Select Status</option>
                        <?php $status = $this->Menu_model->get_status($uid);
                        foreach($status as $st){
                        ?>
                        <option value="<?=$st->id?>"><?=$st->name?></option>
                        <?php } ?>
                    </select>
                    
                    <div id="nstr">
                           <input type="number" name="nschool" class="form-control" placeholder="Total No of School">
                           <input type="number" name="nrvenue" class="form-control" placeholder="Total Revenue">
                       </div>
                    
                    
                    <lable>Expected Date</lable>
                    <input type="date" id="exdate" name="exdate" value="" class="form-control" required="" min="<?=date('Y-m-d');?>">
                   </div>
                   <div class="form-group text-center mt-3">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                    </div>
                    </div>
                    
                  </form>
              
     </div></div>
     <div class="col-sm col-md-12 col-lg-12 m-auto">
         <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>								
                                            <th>SNO</th>
                                            <th>Updated By</th>
                                            <th>Assign Date</th>
                                            <th>Updated Date</th>
                                            <th>Current Action</th>
                                            <th>Action Taken</th>
                                            <th>Purpose Achieved</th>
                                            <th>Remarks</th>
                                            <th>MOM</th>
                                            <th>Last Status</th>
                                            <th>Current Status</th>
                                            <th>Next Status</th>
                                        </tr>
                                    </thead>
                                
                                <tbody id="cmplogs">
                                    
                                </tbody>
                                </table>
                            </div>
                        </div>
                  
                  
                  
                  
              </div>
     </div></div>
     <?php } ?>
    </section>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>

document.getElementById("otherremark1").readOnly = true;
document.getElementById("ans11").readOnly = true;
document.getElementById("ans21").readOnly = true;
document.getElementById("ans31").readOnly = true;

$('#otherremark').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("otherremark1").readOnly = false;}else{
    document.getElementById("otherremark1").value=val;document.getElementById("otherremark1").readOnly = true;}
});

$('#ans1').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("ans11").readOnly = false;}else{
    document.getElementById("ans11").value=val;document.getElementById("ans11").readOnly = true;}
});

$('#ans2').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("ans21").readOnly = false;}else{
    document.getElementById("ans21").value=val;document.getElementById("ans21").readOnly = true;}
});

$('#ans3').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("ans31").readOnly = false;}else{
    document.getElementById("ans31").value=val;document.getElementById("ans31").readOnly = true;}
});




$('#reviewtype').on('change', function b() {
  var reviewtype = document.getElementById("reviewtype").value;
  var currentDate = new Date();
  
  if(reviewtype=='Weekly'){
      currentDate.setDate(currentDate.getDate() - 7);
  }
  if(reviewtype=='Fortnightly'){
      currentDate.setDate(currentDate.getDate() - 15);
  }
  if(reviewtype=='Monthly'){
      currentDate.setDate(currentDate.getDate() - 30);
  }
  if(reviewtype=='Querterly'){
      currentDate.setDate(currentDate.getDate() - 90);
  }
  var formattedDate = currentDate.toISOString().slice(0,10);
  document.getElementById("fixdate").value = formattedDate;
});

function myFunction() {
  var checkBox = document.getElementById("myCheckbox");
  if (checkBox.checked == true){
    document.getElementById("fixdate").readOnly = false;
  } else {
    document.getElementById("fixdate").readOnly = true;
  }
  
}

$('#statusid').on('change', function b() {
var revtype = document.getElementById("revtype").value;   
var pstid = document.getElementById("pstid").value;
var stid = document.getElementById("statusid").value;
var bdid = document.getElementById("bdid").value;
var fdate = document.getElementById("fdate").value;
var categories = document.getElementById("categories").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmpdbypstnst',
type: "POST",
data: {
pstid: pstid,
stid: stid,
bdid: bdid,
fdate: fdate,
categories : categories,
revtype: revtype
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
});



$('#inid').on('change', function b() {
var inid = document.getElementById("inid").value;
var fdate = document.getElementById("fdate").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmpnlog',
type: "POST",
data: {
inid: inid,
fdate: fdate
},
cache: false,
success: function a(result){
$("#cmpdata").html(result);
}
});
});

document.getElementById("nstr").style.display = "none";
$('#exsid').on('change', function b() {
var exsid = this.value;
if(exsid=='7'){
    document.getElementById("nstr").style.display = "block";
}else{
   document.getElementById("nstr").style.display = "none";
}

});


document.getElementById("statusno").style.display = "none";
$('#statusright').on('change', function b() {
var statusright = this.value;
if(statusright=='No'){
    document.getElementById("statusno").style.display = "block";
}else{
   document.getElementById("statusno").style.display = "none";
}
});


$('#inid').on('change', function b() {
var rtype = document.getElementById("rtype").value;
if(rtype=='Roaster'){
      $("#rosterhide").hide();
      $("#taskupdate").show();
      document.getElementById("ntaction").required = false;
      document.getElementById("ntdate").required = false;
      document.getElementById("ntppose").required = false;
      document.getElementById("exsid").required = false;
      document.getElementById("exdate").required = false;
}else{
      $("#rosterhide").show();
      $("#taskupdate").hide();
      document.getElementById("ntaction").required = true;
      document.getElementById("ntdate").required = true;
      document.getElementById("ntppose").required = true;
      document.getElementById("exsid").required = true;
      document.getElementById("exdate").required = true;
}  
    
var inid = document.getElementById("inid").value;
var fdate = document.getElementById("fdate").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmplogs',
type: "POST",
data: {
inid: inid,
fdate: fdate
},
cache: false,
success: function a(result){
$("#cmplogs").html(result);
}
});
});





$("#orrr").hide();
$('#statusid').on('change', function b() {
var stid = document.getElementById("statusid").value;

if(stid==1){$("#orrr").show();}
else if(stid==8){$("#orrr").show();}
else if(stid==2){$("#orrr").show();}
else{$("#orrr").hide();}



$.ajax({
url:'<?=base_url();?>Menu/getotherremark',
type: "POST",
data: {
stid: stid
},
cache: false,
success: function a(result){
$("#otherremark").html(result);
}
});

$.ajax({
url:'<?=base_url();?>Menu/getcallremark',
type: "POST",
data: {
stid: stid
},
cache: false,
success: function a(result){
$("#ans1").html(result);
}
});

$.ajax({
url:'<?=base_url();?>Menu/getemailremark',
type: "POST",
data: {
stid: stid
},
cache: false,
success: function a(result){
$("#ans2").html(result);
}
});


$.ajax({
url:'<?=base_url();?>Menu/getmeetremark',
type: "POST",
data: {
stid: stid
},
cache: false,
success: function a(result){
$("#ans3").html(result);
}
});



});



$('#ntaction').on('change', function f() {
    var sid = document.getElementById("ntstatus").value;
    var aid = document.getElementById("ntaction").value;
    $.ajax({
        url:'<?=base_url();?>Menu/getpurpose',
        type: "POST",
        data: {
        sid: sid,
        aid: aid
        },
        cache: false,
        success: function a(result){
        $("#ntppose").html(result);
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
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
           
           $revst = $this->Menu_model->get_reviewstarted($myid);
           if($revst){$bdid = $revst[0]->bdid;}else{$bdid=0;}
           if($bdid==0){
           ?>
           
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h3 class="text-center">Plan Review Task</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/planreview" method="post">
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" name="uid" value="<?=$myid?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <lable>Review Start Date</lable>
                        <input type="datetime-local" name="plandate" value="<?=date('Y-m-d H:i:s')?>" class="form-control" required="">
                        
                        <div class="invalid-feedback">Please provide Plan Date Time.</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="mt-4">
                            <select class="form-control" name="reviewtype" required="" id="reviewtype">
                                <option value="Roaster">Roaster</option>
                                <option value="Weekly">Weekly</option>
                                <option value="Fortnightly">Fortnightly</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Querterly">Querterly</option>
                            </select>
                        </div>
                        
                        <input type="checkbox" id="myCheckbox" onclick="myFunction()">
                        <label>Do You Want to Change Period Time Frame.</label><br>
                        
                        <lable>Review Period</lable>
                        <input type="date" name="fixdate" id="fixdate" value="<?=date('Y-m-d')?>"  class="form-control" required="" readOnly>
                        
                        
                        <div class="mt-4">
                            <select class="form-control" name="bdid" required="">
                                <?php 
                                $udetail = $this->Menu_model->get_userbyid($myid);
                                  $admid = $udetail[0]->admin_id;
                                $bd = $this->Menu_model->get_userbyaid($admid);
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
                  <h3 class="text-center">Start Review Task</h3>
                  <hr>
                  <form action="<?=base_url();?>Menu/startreview" method="post">
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" name="uida" value="<?=$myid?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <input type="datetime-local" name="startt" value="<?=date('Y-m-d H:i:s')?>" class="form-control" readonly>
                        <div class="invalid-feedback">Please provide Start Date Time.</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="mt-4">
                            <select class="form-control" name="reviewid" required="">
                                <?php $reviewid = $this->Menu_model->get_reviewid($myid);
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
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Srart Review</button>
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
                  <h4 class="text-center"><?=$revst[0]->reviewtype;?> Review</h4>
                  <h5 class="text-center"><?=$revst[0]->name;?></h5>
                  <hr>
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" id="mrid" value="<?=$revst[0]->rid;?>">
                        <input type="hidden" name="uidaa" value="<?=$myid?>">
                        <input type="hidden" id="revtype" value="<?=$revst[0]->reviewtype;?>">
                        <input type="hidden" id="bdid" value="<?=$revst[0]->bdid;?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <input type="date" id="fdate" name="fdate" value="<?=$revst[0]->fixdate;?>" class="form-control" readonly>
                        <input type="hidden" id="pstid" value="<?=$myid?>">
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
                                <?php $status = $this->Menu_model->get_status($myid);
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
                      <?php
                          $rid = $revst[0]->rid;
                          $gscount = $this->Menu_model->get_goalsbyrid($rid);
                          $gscount = $gscount[0]->cont; ?>
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
                  <?php if($gscount>0){echo '<p class="font-italic text-danger mt-3">note: You Are Seted Goal on this Review.</p>';}else{?>
                  <center><button type="submit" class="btn btn-success" onclick="showgs()">Click for Goal Setting</button></center>
                  <p class="font-italic text-danger mt-3">note: Before Close Review You Need to Set Goal.</p>
                  <?php } ?>
                  </div>
                  
              </div>
              
              
             
         </div>
     </div></div>
     <div class="col-sm col-md-6 col-lg-6 m-auto">
         <div class="card card-primary card-outline">
             <form action="<?=base_url();?>Menu/pstrtaskc" method="post">
              <div class="card-body box-profile" id="cmpdata">
              </div>
                  <div class="was-validated m-3">
                    <div class="form-group">
                        <textarea class="form-control" name="remark" placeholder="Review Remark..."  required=""></textarea>
                    </div>
                    <hr>
                   <input type="hidden" name="pstuid" value="<?=$myid?>">
                   <input type="hidden" name="bduid" value="<?=$bdid?>">
                   <input type="hidden" name="rid" value="<?=$revst[0]->rid;?>">
                   <hr>
                   <input type="hidden" name="pstuid" value="<?=$myid?>">
                       <input type="hidden" name="bduid" value="<?=$bdid?>">
                       <input type="hidden" name="rid" value="<?=$revst[0]->rid;?>">
                       <lable>Is the task planning frequency is good for 2023-24?</lable>
                       <textarea name="ans1" class="form-control"></textarea>
                       <lable>Is the 360 degre communication used? </lable>
                       <textarea name="ans2" class="form-control"></textarea>
                       <lable>Is the enough research done?</lable>
                       <textarea name="ans3" class="form-control"></textarea>
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
                       <hr>
                   <center><h5>Create Task</h5></center>
                   <input type="datetime-local" name="ntdate" value="<?=date('Y-m-d H:i:s');?>" class="form-control" required="">
                   <lable>Select Action</lable>
                   <select id="ntaction" name="ntaction" class="form-control"  required="">
                       <option value="">Select Action</option>
                       <?php $action = $this->Menu_model->get_action();
                       foreach($action as $a){?>
                           <option value="<?=$a->id?>"><?=$a->name?></option>
                       <?php } ?>
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
                    
                    <lable>Expected Date</lable>
                    <input type="date" name="exdate" value="" class="form-control" required="" min="<?=date('Y-m-d');?>">
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
var rid = document.getElementById("mrid").value;
var revtype = document.getElementById("revtype").value;    
var pstid = document.getElementById("pstid").value;
var stid = document.getElementById("statusid").value;
var bdid = document.getElementById("bdid").value;
var fdate = document.getElementById("fdate").value;
var categories = document.getElementById("categories").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmpdbyadmins',
type: "POST",
data: {
pstid: pstid,
stid: stid,
bdid: bdid,
fdate: fdate,
categories : categories,
revtype: revtype,
rid: rid
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
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


$('#inid').on('change', function b() {
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
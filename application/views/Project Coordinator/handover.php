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
          </div><!-- /.col -->
          <div class="col-sm-6">
            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Handover to Operations</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="<?=base_url();?>Menu/bdHandover" method="post" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="was-validated">
                    <div class="form-row">
                        <div class="col-lg-4 p-3">
                              <div class="form-group">
                                <label for="validationSample01">Client Name</label>
                                <input type="hidden" class="form-control" name="bdid" value="<?=$uid?>" required readonly>
                                <input type="text" class="form-control" name="client_name" id="client_name" placeholder="Client Name" value="<?=$md->compname?>" required readonly>
                                <input type="hidden" name="client_id" id="client_id" value="<?php echo $cid;?>">
				<div class="invalid-feedback">Please provide a Company name.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="validationSample02">NGO / Mediator if any</label>
                                <input type="text" class="form-control" name="mediator" id="mediator" placeholder="NGO / Mediator if any" required="">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="noofschool">Number of Schools</label>
                                <input type="text" class="form-control" name="noofschool" id="noofschool" placeholder="Number of Schools" required value="<?=$md->noofschools?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="location">Location / Village</label>
                                <textarea class="form-control" name="location" id="location" placeholder="Location / Village" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" name="city" id="city" placeholder="City" required value="<?=$md->city?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" class="form-control" name="state" id="state" placeholder="State" required value="<?=$md->state?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="state">FTTP Type</label>
                                <select class="form-control" name="fttptype">
                                    <option>Individual FTTP</option>
                                    <option>Cluster FTTP</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                        </div>
                        

                        <div class="col-lg-4 p-3">
                            <div class="form-group">
                                <label>Are the schools to be Identified?</label>
                                <select class="form-control" name="are_spd_identify" id="are_spd_identify" placeholder="Are the schools to be Identified?" required>
                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="spd_identify_by">School Identification by</label>
                                <select class="form-control" name="spd_identify_by" id="spd_identify_by" placeholder="School Identification by" onchange="schoolFun()" required>
                                    <option value="">Identification Type</option>
                                    <option>STEM</option>
                                    <option>Client</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              
                              <div class="form-group">
                                <label>Identification Process</label>
                                <select class="form-control" name="idprocess" id="idprocess" placeholder="School Identification by" required>
                                    <option value="No Need">No Need</option>
                                    <option value="Post">Identification After Handover</option>
                                    <option value="Pre">Identification Before Handover</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              
                              <div class="form-group">
                                <label for="infrastructure">School Infrastructure for MSC (platform)</label>
                                <input type="text" class="form-control" name="infrastructure" id="infrastructure" placeholder="School Infrastructure for MSC (platform)" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Contact Person" value="<?=$md->contactperson?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="cp_mno">Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="cp_mno" id="cp_mno" placeholder="Contact Person Mobile No" value="<?=$md->phoneno?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="acontact_person">Alternate Contact Person</label>
                                <input type="text" class="form-control" name="acontact_person" id="acontact_person" placeholder="Alternate Contact Person" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="acp_mno">Alternate Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="acp_mno" id="acp_mno" placeholder="Alternate Contact Person Mobile No" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              
                        </div>
                        <div class="col-lg-4 p-3">
                              <div class="form-group">
                                <label for="language">Language on backdrop required by client for MSC</label>
                                <input type="text" class="form-control" name="language" id="language" placeholder="Language on backdrop required by client for MSC" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="expected_installation_date">Expected Installation Date by Client/Sales Team</label>
                                <input type="date" class="form-control" name="expected_installation_date" id="expected_installation_date" placeholder="Expected Installation Date by Client/Sales Team" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="project_tenure">Project Tenure (Year)</label>
                                <input type="number" class="form-control" name="project_tenure" id="project_tenure" placeholder="Project Tenure" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="project_type">Project Type</label>
                                <select id="projectt" onchange="prot()" class="form-control" required>
                                    <option value="">Select Project Type</option>
                                    <option>MSC</option>
                                    <option>Other</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                                <input type="text" class="form-control" name="project_type" id="project_type" placeholder="Project Type" required readonly>
                              </div>
                              <div class="form-group">
                                <label for="comments">Special Requirements / Comments :</label>
                                <textarea class="form-control" name="comments" id="comments" placeholder="Special Requirements / Comments" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="comments">Artwork Inputs :</label>
                                <textarea class="form-control" name="remark" id="remark" placeholder="Artwork Inputs" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="filname">Client logo (Attech Multiple Logos)</label>
                                <input type="file" class="form-control-file" id="filname" name="filname[]" required multiple>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                        </div>
                    </div>
                  </div>
                  
                </div>
                <div class="row p-3">
                    <?php if($spd!='0'){foreach($spd as $spd){?>
                            <div class="card p-3 col-12"><b><input name="sid[]" class="form-check-input" type="checkbox" value="<?=$spd->id?>" checked><?=$spd->sname?><?=$spd->saddress?>,<?=$spd->scity?>,<?=$spd->sstate?></b></div>
                    <?php }?>
                    <div class="card p-3 col-12"><b><input id="allcheck" class="form-check-input" type="checkbox" onchange="toggleButton()"> Thank you for completing the form. Before you submit, please take a moment to review your responses for accuracy and completeness. Once you click the 'Submit' button, your form will be processed, and we will consider the information provided as final.</b></div>
                    <?php } ?> 
                </div>
                
                <div class="p-3" id="sdetail">
                    <div class="row">
                        <input type="text" name="sname[]" class="form-control col-3" placeholder="School Name">
                        <input type="text" name="saddress[]" class="form-control col-3" placeholder="Address">
                        <input type="text" name="scity[]" class="form-control col-3" placeholder="City">
                        <input type="text" name="sstate[]" class="form-control col-3" placeholder="State">
                        <input type="text" name="scontact[]" class="form-control col-3" placeholder="Contact No">
                        <input type="text" name="sdegignation[]" class="form-control col-3" placeholder="Designation">
                        <input type="number" name="snumber[]" class="form-control col-3" placeholder="Contact No">
                    </div>
                    <div class="row">
                        <input type="text" name="sname[]" class="form-control col-3" placeholder="School Name">
                        <input type="text" name="saddress[]" class="form-control col-3" placeholder="Address">
                        <input type="text" name="scity[]" class="form-control col-3" placeholder="City">
                        <input type="text" name="sstate[]" class="form-control col-3" placeholder="State">
                        <input type="text" name="scontact[]" class="form-control col-3" placeholder="Contact No">
                        <input type="text" name="sdegignation[]" class="form-control col-3" placeholder="Designation">
                        <input type="number" name="snumber[]" class="form-control col-3" placeholder="Contact No">
                    </div>
                    <div class="row">
                        <input type="text" name="sname[]" class="form-control col-3" placeholder="School Name">
                        <input type="text" name="saddress[]" class="form-control col-3" placeholder="Address">
                        <input type="text" name="scity[]" class="form-control col-3" placeholder="City">
                        <input type="text" name="sstate[]" class="form-control col-3" placeholder="State">
                        <input type="text" name="scontact[]" class="form-control col-3" placeholder="Contact No">
                        <input type="text" name="sdegignation[]" class="form-control col-3" placeholder="Designation">
                        <input type="number" name="snumber[]" class="form-control col-3" placeholder="Contact No">
                    </div>
                    
                    <div class="row">
                        <input type="text" name="sname[]" class="form-control col-3" placeholder="School Name">
                        <input type="text" name="saddress[]" class="form-control col-3" placeholder="Address">
                        <input type="text" name="scity[]" class="form-control col-3" placeholder="City">
                        <input type="text" name="sstate[]" class="form-control col-3" placeholder="State">
                        <input type="text" name="scontact[]" class="form-control col-3" placeholder="Contact No">
                        <input type="text" name="sdegignation[]" class="form-control col-3" placeholder="Designation">
                        <input type="number" name="snumber[]" class="form-control col-3" placeholder="Contact No">
                    </div>
                    <div class="row">
                        <input type="text" name="sname[]" class="form-control col-3" placeholder="School Name">
                        <input type="text" name="saddress[]" class="form-control col-3" placeholder="Address">
                        <input type="text" name="scity[]" class="form-control col-3" placeholder="City">
                        <input type="text" name="sstate[]" class="form-control col-3" placeholder="State">
                        <input type="text" name="scontact[]" class="form-control col-3" placeholder="Contact No">
                        <input type="text" name="sdegignation[]" class="form-control col-3" placeholder="Designation">
                        <input type="number" name="snumber[]" class="form-control col-3" placeholder="Contact No">
                    </div>
                    <div class="row">
                        <input type="text" name="sname[]" class="form-control col-3" placeholder="School Name">
                        <input type="text" name="saddress[]" class="form-control col-3" placeholder="Address">
                        <input type="text" name="scity[]" class="form-control col-3" placeholder="City">
                        <input type="text" name="sstate[]" class="form-control col-3" placeholder="State">
                        <input type="text" name="scontact[]" class="form-control col-3" placeholder="Contact No">
                        <input type="text" name="sdegignation[]" class="form-control col-3" placeholder="Designation">
                        <input type="number" name="snumber[]" class="form-control col-3" placeholder="Contact No">
                    </div>
                    <div class="card p-3 col-12"><b><input id="allcheck" class="form-check-input" type="checkbox" onchange="toggleButton()"> Thank you for completing the form. Before you submit, please take a moment to review your responses for accuracy and completeness. Once you click the 'Submit' button, your form will be processed, and we will consider the information provided as final.</b></div>
                </div>
                
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" id="btn" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>

document.getElementById("sdetail").style.display = "none";

document.getElementById('btn').disabled = true;

function toggleButton() {
    var checkbox = document.getElementById('allcheck');
    if (checkbox.checked) {
        document.getElementById('btn').disabled = false;
      }
    else {
        document.getElementById('btn').disabled = true;
      }
}

$('#idprocess').on('change', function b() {
    var idprocess = this.value;
    if(idprocess=='Pre'){
        document.getElementById('btn').disabled = true;
    }
    if(idprocess=='Post'){
        document.getElementById('btn').disabled = false;
    }
    if(idprocess=='No Need'){
        document.getElementById('btn').disabled = false;
    }
    
    
    
});


$('#spd_identify_by').on('change', function b() {
var ideby = document.getElementById("spd_identify_by").value;
if(ideby=='Client'){
    document.getElementById("sdetail").style.display = "block";
}
});




function prot(){
    var x = document.getElementById("projectt").value;
    if(x!='Other'){
        document.getElementById("project_type").value = x;
        document.getElementById("project_type").readOnly = true;
    }else{
        document.getElementById("project_type").value = '';
        document.getElementById("project_type").readOnly = false;
    }
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
url:'<?=base_url();?>Menu/getcity',
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
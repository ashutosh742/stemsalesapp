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
                                <input type="text" class="form-control" name="client_name" id="client_name" placeholder="Client Name" value="<?=$md->client_name?>" required readonly>
                                <div class="invalid-feedback">Please provide a Company name.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="validationSample02">NGO / Mediator if any</label>
                                <input type="text" class="form-control" name="mediator" id="mediator" placeholder="NGO / Mediator if any" required="" value="<?=$md->mediator?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="noofschool">Number of Schools</label>
                                <input type="text" class="form-control" name="noofschool" id="noofschool" placeholder="Number of Schools" required value="<?=$md->noofschool?>">
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="location">Location / Village</label>
                                <textarea class="form-control" name="location" id="location" placeholder="Location / Village" required value="<?=$md->location?>"></textarea>
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
                        </div>
                        

                        <div class="col-lg-4 p-3">
                            <div class="form-group">
                                <label for="spd_identify_by">Are the schools to be Identified?</label>
                                <select class="form-control" name="are_spd_identify" id="are_spd_identify" value="<?=$md->spd_identify_by?>" required>
                                    <option>Yes</option>
                                    <option>No</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="spd_identify_by">School Identification by</label>
                                <select class="form-control" name="spd_identify_by" id="spd_identify_by" onchange="schoolFun()" required>
                                    <option value="">Identification Type</option>
                                    <option>STEM</option>
                                    <option>Client</option>
                                </select>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="infrastructure">School Infrastructure for MSC (platform)</label>
                                <input type="text" class="form-control" name="infrastructure" id="infrastructure" value="<?=$md->infrastructure?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Contact Person" value="<?=$md->contact_person?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="cp_mno">Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="cp_mno" id="cp_mno" placeholder="Contact Person Mobile No" value="<?=$md->cp_mno?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="acontact_person">Alternate Contact Person</label>
                                <input type="text" class="form-control" name="acontact_person" id="acontact_person" value="<?=$md->acontact_person?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="acp_mno">Alternate Contact Person Mobile No</label>
                                <input type="text" class="form-control" name="acp_mno" id="acp_mno" value="<?=$md->acp_mno?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              
                        </div>
                        <div class="col-lg-4 p-3">
                              <div class="form-group">
                                <label for="language">Language on backdrop required by client for MSC</label>
                                <input type="text" class="form-control" name="language" id="language" value="<?=$md->language?>" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="expected_installation_date">Expected Installation Date by Client/Sales Team</label>
                                <input type="date" class="form-control" value="<?=$md->expected_installation_date?>" name="expected_installation_date" id="expected_installation_date" placeholder="Expected Installation Date by Client/Sales Team" required>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="project_tenure">Project Tenure (Year)</label>
                                <input type="number" class="form-control" value="<?=$md->project_tenure?>" name="project_tenure" id="project_tenure" placeholder="Project Tenure" required>
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
                                <input type="text" class="form-control" name="project_type" id="project_type" value="<?=$md->project_type?>" placeholder="Project Type" required readonly>
                              </div>
                              <div class="form-group">
                                <label for="comments">Special Requirements / Comments :</label>
                                <textarea class="form-control" name="comments" id="comments" value="<?=$md->comments?>" required></textarea>
                                <div class="invalid-feedback">Please provide a Information.</div>
                                <div class="valid-feedback">Looks good!</div>
                              </div>
                              <div class="form-group">
                                <label for="wono">Work Order No</label>
                                <input type="text" class="form-control" name="wono" id="wono" value="<?=$md->comments?>" required>
                              </div>
                              <div class="form-group">
                                <label for="porno">Purchase Order No</label>
                                <input type="text" class="form-control" name="porno" id="porno" value="<?=$md->comments?>" required>
                              </div>
                              <div class="form-group">
                                <label for="gstno">GST Number</label>
                                <input type="text" class="form-control" name="gstno" id="gstno" value="<?=$md->comments?>" required>
                              </div>
                              <div class="form-group">
                                <label for="panno">Pan Number</label>
                                <input type="text" class="form-control" name="panno" id="panno" value="<?=$md->comments?>" required>
                              </div>
                              <div class="form-group">
                                <label for="panno">Total Budget</label>
                                <input type="number" class="form-control" name="tbudget" id="tbudget" value="<?=$md->comments?>" required onchange="Budgetper()">
                              </div>
                              <div class="form-group">
                                <label for="payterm">PAYMENT TERMS</label>
                                <textarea type="text" class="form-control" name="payterm" id="payterm" value="<?=$md->comments?>"></textarea>
                              </div>
                              <div class="form-group">
                                <label for="pwosdate">Purchase Order & Work Order Signed Date</label>
                                <input type="date" class="form-control" name="pwosdate" id="pwosdate" value="<?=$md->comments?>">
                              </div>
                              <div class="form-group">
                                <label for="moudate">MoU Signed Date</label>
                                <input type="date" class="form-control" name="moudate" id="moudate" value="<?=$md->comments?>">
                              </div>
                              <div class="form-group">
                                <label for="srfinovice">Special Requirement for Invoice</label>
                                <textarea class="form-control" name="srfinovice" id="srfinovice" value="<?=$md->comments?>"></textarea>
                              </div>
                        </div>
                    </div>
                  </div>
                  
                </div>
                <div class="row" id="container">
                    <div id="mainsection" class="col-lg-12 col-sm p-3">
                        <div id="mainsection" class="row p-3">
                        <div class="col-lg-4 p-1 col-sm"><input type="text" class="form-control" name="sname[]" placeholder="School Name"></div>
                        <div class="col-lg-4 p-1 col-sm"><input type="text" class="form-control" name="saddress[]" placeholder="Address with Pincode"></div>
                        <div class="col-lg-4 p-1 col-sm"><input type="text" class="form-control" name="scity[]" placeholder="City"></div>
                        </div>
                        <div id="mainsection" class="row p-3">
                        <div class="col-lg-4 p-1 col-sm"><input type="text" class="form-control" name="sstate[]" placeholder="State"></div>
                        <div class="col-lg-4 p-1 col-sm"><input type="text" class="form-control" name="contact_name[]" placeholder="Contact Person"></div>
                        <div class="col-lg-4 p-1 col-sm"><input type="text" class="form-control" name="contact_no[]" placeholder="Contact No"></div>
                        </div><hr>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
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
function schoolFun() {
  var x = document.getElementById("noofschool").value;
  var ideby = document.getElementById("spd_identify_by").value;
  for(var i=1;i<x;i++){
      var container = document.getElementById("container");
      var section = document.getElementById("mainsection");
      container.appendChild(section.cloneNode(true));
  }
}

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
      
</script>


<script type='text/javascript'>

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
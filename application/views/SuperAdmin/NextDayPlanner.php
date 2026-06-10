<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM HR APP</title>

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
            <h1 class="m-0">Add New Candidate</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
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
              <div class="card-header">
              </div>
              <!-- /.card-header -->
              <div class="card-body box-profile p-5">
                  
              <div class="card p-3">
                    <form class="" action="<?php echo base_url(); ?>excel_import/import" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="storeby" value="<?=$uid?>">
            			<input type="file" name="excel" required>
            			<button type="submit" name="import">Import</button>
            		</form>
            		<a href="<?=base_url();?>uploads/format/Candidate.xlsx" target="_blank">Download Format</a>
        		</div>
        		
        <?php
        
        
        
		if(isset($_POST["import"])){
			$fileName = $_FILES["excel"]["name"];
			$fileExtension = explode('.', $fileName);
            $fileExtension = strtolower(end($fileExtension));
			$newFileName = date("Y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;

			$targetDirectory = "uploads/" . $newFileName;
			move_uploaded_file($_FILES['excel']['tmp_name'], $targetDirectory);

			error_reporting(0);
			ini_set('display_errors', 0);
			
			
			
			require base_url().'excelReader/excel_reader2.php';
			require base_url().'excelReader/SpreadsheetReader.php';
			
			

			$reader = new SpreadsheetReaderA($targetDirectory);
			echo 'djfghkdj';
			
			foreach($reader as $key => $row){
			    
				echo $fullname = $row[0];
				echo $user_name = $row[1];
				echo $user_pass = $row[2];
				echo $department = $row[3];
				echo $role = $row[4];
				echo $barcode = $row[5];
				echo $photo =  $row[6];
				echo $luser =  $row[7];
				
				
				
			}

			echo
			"
			
			";
		}
		?>
                  
                  
                  
                  
              <!-- form start -->
            <form method="post" action="<?=base_url();?>Menu/NewCandidate">
                <input type="hidden" name="storeby" value="<?=$uid?>">
            <div class="form-row">
              <div class="col-sm-12 col-lg-6 p-3">
                <div class="was-validated">
                    <div class="form-row">
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">Name</label>
                            <input type="text" class="form-control"  placeholder="Candidate Name" value="" required="" name="cname" >
                            <div class="invalid-feedback">Please provide a Candidate name.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        
                        
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">address</label>
                            <textarea class="form-control"  placeholder="Address" value="" required="" name="address" ></textarea>
                            <div class="invalid-feedback">Please provide a Address.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">city</label>
                            <input type="text" class="form-control"  placeholder="City" value="" required="" name="city" >
                            <div class="invalid-feedback">Please provide a City.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        
                        <div class="col-12 col-md-12">
                            <label for="validationSample01">state</label>
                            <input type="text" class="form-control"  placeholder="State" value="" required="" name="state" >
                            <div class="invalid-feedback">Please provide a State.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        
                        
                        <div class="col-12 col-md-12 mb-12">
                            <label for="validationSample02">Email Id</label>
                            <input type="text" class="form-control" id="cemail" placeholder="Email ID" name="cemail"  required="">
                            <div class="invalid-feedback">Please provide a Email ID.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="col-sm-12 col-lg-6 p-3">
                <div class="was-validated">
                    <div class="form-row">
                        
                    <div class="col-12 col-md-12 mb-12">
                        <label for="validationSample03">Mobile No</label>
                        <input type="text" class="form-control" id="cmo" name="cmo" placeholder="Mobile No" required="">
                        <div class="invalid-feedback">Please provide a Mobile No.</div>
                        <div class="valid-feedback">Looks good!</div>
                    </div>
                    
                    
                    <div class="col-12 col-md-12 mb-12">
                        <label for="validationSample03">WhatsApp No</label>
                        <input type="text" class="form-control" name="cwno" placeholder="WhatsApp No" required="">
                        <div class="invalid-feedback">Please provide a WhatsApp No.</div>
                        <div class="valid-feedback">Looks good!</div>
                    </div>
                        
                    <div class="col-12 col-md-12 mb-12">
                        <label for="validationSample03">Qualification</label>
                        <input type="text" class="form-control" name="cqual" placeholder="Qualification" required="">
                        <div class="invalid-feedback">Please provide a Qualification</div>
                        <div class="valid-feedback">Looks good!</div>
                    </div>
                        
                    <div class="col-12 col-md-12" id="budgetdiv">
                        <label for="validationSample01">Interested Field</label>
                        <input type="text" name="cifield" class="form-control" id="cifield" placeholder="Interested Field" value="" required=""  >
                        <div class="invalid-feedback">Please provide a Interested Field.</div>
                        <div class="valid-feedback">Looks good!</div>
                    </div>
                    
                    <div class="col-12 col-md-12" id="budgetdiv">
                        <label for="validationSample01">Select Designation</label>
                         <select name="position_id" class="form-control" required="">
                              <option>Select Designation</option>
                              <?php foreach($position as $position){?>
                              <option value="<?=$position->id?>"><?=$position->name?></option>
                              <?php } ?>
                          </select>
                        <div class="invalid-feedback">Please provide a Interested Field.</div>
                        <div class="valid-feedback">Looks good!</div>
                    </div>
                    
                    
                    
                    <div class="col-12 col-md-12 mb-12">
                        <label for="validationSample02">Resume</label>
                        <input type="file"  name="cresume" class="form-control" required>
                        <div class="invalid-feedback">Please provide a valid Resume.</div>
                        <div class="valid-feedback">Looks good!</div>
                    </div>
                     
                    </div>
                    
                </div>
                
            </div>
            
          </div>
          <center><button class="btn btn-primary mt-4" type="submit">Submit</button></center>
        </form>
        
        
        
            
              
            </div>
            <!-- /.card -->
  </div>
  </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
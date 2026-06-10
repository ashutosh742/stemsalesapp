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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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
            <h1 class="m-0">Create Request</h1>
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
      <form action="<?=base_url();?>Menu/bdrequest" method="post" enctype="multipart/form-data">
        <div class="row p-3">
          <div class="col-sm col-md-6 col-lg-6">
            <div class="card card-primary card-outline">
				      <div class="card-body box-profile">
                <input type="hidden" name="uid" value="<?=$uid?>">
                <input type="hidden" name="stage_type" value="bd-request">
                <div class="form-group">
                  <label for="task_type">Client Type</label><br>
                  <!-- <select class="custom-select rounded-0" name="ctype" id="ctype">
                      <option>Select Request</option>
                      <option>On Board Client</option>
                      <option>New Client</option>
                  </select> -->
                  <input type="radio"name="ctype"class="ctype"value="New Client"required=""id="ctypenew"/>&nbsp;&nbsp;New Client &nbsp;&nbsp;
                  <input type="radio"name="ctype"class="ctype"value="On Board Client"required=""id="ctypeonboard"/>&nbsp;&nbsp;On Board Client &nbsp;&nbsp;
                </div>
                <div class="form-group">
                  <label for="cname">Client</label>
                  <select class="custom-select rounded-0" name="cid" id="cid" required>
                    <option value="">Select Company</option>
                    <?php foreach($fannal as $d){
                      if($d->cstatus == '3' || $d->cstatus == '6' || $d->cstatus == '9' || $d->cstatus == '12' || $d->cstatus == '13'){
                    ?>
                    <option class='newclientstatus' value='<?=$d->id?>'><?=$d->compname?></option>
                    <?php }else if($d->cstatus =='7'){?>
                    <option value='<?=$d->id?>' class='onboardclientstatus'><?=$d->compname?></option>
                    <?php
                    }} ?>
                  </select>
                  <input type="hidden" name="cname" id="cname" />
                  <div style="display:block" id="clientdetail">

                  </div>
                </div>
                <div class="form-group">
                  <label for="task_type">Request Type</label>
                  <select required class="custom-select rounded-0" name="request_type" id="task_type">
                </select>
                </div>
                <div class="form-group">
                  <label for="task_date">Target Date</label>
                  <input type="date" required class="form-control" name="targetd" id="task_date" >
                </div>
                <div class="form-group">
                  <label for="remark">Request Detail (Be specific)</label>
                  <textarea type="text" class="form-control" name="remark" id="remark" placeholder="Request Detail" required></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm col-md-6 col-lg-6">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div id="test1" style="display: none;">
                  <div class="form-group">
                    <label>Project Code</label>
                    <select class="custom-select rounded-0" id="pcode" name="pcode">
                    </select>
                  </div>
                  <div class="form-group mt-5">
                    <div class="alert alert-primary" role="alert" id="cinfo">
                      Info
                    </div>
                  </div>
                </div>
                <div id="test2" style="display: none;">
                  <div class="form-group">
                      <label for="ccperson">Contact Person Name</label>
                      <input type="text" class="form-control" name="ccperson" id="ccperson" placeholder="Contact Person Name">
                  </div>
                  <div class="form-group">
                      <label for="ccpmno">Contact Person Mobile No</label>
                      <input type="text" class="form-control" name="ccpmno" id="ccpmno" placeholder="Contact Person Mobile No">
                  </div>
                  <div class="form-group">
                      <label for="visitdt">Date Time</label>
                      <input type="datetime-local" class="form-control" name="visitdt" id="visitdt" >
                  </div>
                  <!-- <div class="form-group">
                      <label for="">Select No of location</label>
                      <select class="custom-select rounded-0" name="m_id" id="m_id">
                        <option value="1">One</option>
                        <option value="2">Multiple</option>
                      </select>
                  </div> -->
                  <div class="form-group">
                    <label for="caddress">Location</label>
                    <textarea type="text" class="form-control" name="caddress" id="caddress" placeholder="Client Address"></textarea>
                  </div>
                  <div class="form-group">
                      <label for="expectation">Expectation</label>
                      <input type="text" class="form-control" name="expectation" id="expectation" placeholder="Expectation">
                  </div>
                </div> 
                <div id="test3" style="display: none;">
                  <div class="form-group">
                      <label>Client Name</label>
                      <input type="text" class="form-control" placeholder="Client Name" name="cnamec">
                  </div>
                  <div class="form-group">
                      <label for="caddress">Client Address</label>
                      <textarea type="text" class="form-control" name="caddress" id="caddress" placeholder="Client Address"></textarea>
                  </div>
                  <div class="form-group">
                      <label for="ccperson">Contact Person Name</label>
                      <input type="text" class="form-control" name="ccperson" id="ccperson" placeholder="Contact Person Name">
                  </div>
                  <div class="form-group">
                      <label for="ccpmno">Contact Person Mobile No</label>
                      <input type="text" class="form-control" name="ccpmno" id="ccpmno" placeholder="Contact Person Mobile No">
                  </div>
                  <div class="form-group">
                      <label for="visitdt">Demo Date Time</label>
                      <input type="datetime-local" class="form-control" name="visitdt" id="visitdt" >
                  </div>
                  
                  <div class="form-group">
                      <label for="visitdt">Metting Type</label>
                      <select class="custom-select rounded-0" name="m_id" id="m_id">
                          <option value="">Metting Type</option>
                          <option value="Online">Online</option>
                          <option value="Offline">Offline</option>
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="expectation">Expectation</label>
                      <input type="text" class="form-control" name="expectation" id="expectation" placeholder="Expectation">
                  </div>
                </div>
                <div id="test4" style="display: none;">
                </div>
                <div id="test5" style="display: none;">
                          
                    
                </div>
                <div id="test6" style="display: none;">
                  <div class="form-group">
                      <label>Identification Type</label>
                      <select class="custom-select rounded-0" name="idetype">
                          <option>Physical</option>
                          <option>virtual</option>
                          <option>Mix</option>
                      </select>
                  </div>
                  <div class="form-group">
                      <label>Type of School</label>
                      <select class="custom-select rounded-0" name="tyschool">
                          <option>Govt.</option>
                          <option>Private</option>
                          <option>Mix</option>
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="noschool">NO of School</label>
                      <input type="text" class="form-control" name="noschool" id="noschool" placeholder="NO of School">
                  </div>
                  <div class="form-group" id="hidelocation">
                      <label for="">Select No of locations</label>
                      <select class="custom-select rounded-0" name="nooflocation" id="nooflocation">
                        <option value="1">One</option>
                        <option value="2">Multiple</option>
                      </select>
                  </div>
                  <!-- <div class="form-group">
                      <label for="location">Location</label>
                      <textarea class="form-control" name="location" id="location" placeholder="Location"></textarea>
                  </div> -->
                  
                  <div class="form-group hideon">
                    <label for="location">Location</label>
                    <div class="input-group">
                        <textarea class="form-control" name="location_n[]" id="location" placeholder="Location"></textarea>
                        
                            
                            <!-- <span class="input-group-text delete-input"><i class="fas fa-minus"></i></span> -->
                        
                    </div>
                  </div>
                  <div class="row multilocation">
                    <div class="col-6">
                      <div class="form-group">
                        <label for="noschool">NO of School</label>
                        <input type="number" class="form-control" name="noschoolbylocation[]" id="noschoolbylocation" placeholder="NO of School">
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label for="location">Location</label>
                        <div class="input-group">
                          <textarea class="form-control" name="location_n[]" id="location_n" placeholder="Location"></textarea>
                          <div class="input-group-append">
                            <span class="input-group-text plus-icon"><i class="fas fa-plus"></i></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="more"></div>
                  <div class="form-group">
                    <label>Attach NGO Letter (only pdf)</label>
                    <input type="file" class="form-control" id="filname" name="filname">
                  </div>
                  <div class="form-group">
                    <label>School Letter Required</label>
                    <select class="custom-select rounded-0" name="sletter" id="sletter">
                      <option value="No">No</option>    
                      <option value="Yes">Yes</option>
                          
                    </select>
                  </div>
                  <div class="form-group" style="display:none" id="sletterattach">
                    <label>Attach School Letter format you need from Operation</label>
                    <input type="file" class="form-control" id="sletterfile" name="sletterfile">
                  </div>
                  <div class="form-group">
                    <label>DM/DEO Letter Required</label>
                    <select class="custom-select rounded-0" name="dmletter" id="dmletter">
                      <option value="No">No</option>    
                      <option value="Yes">Yes</option>
                    </select>
                  </div>
                  <div class="form-group" style="display:none" id="dmletterattach">
                    <label>Attach DM/DEO Letter format you need from Operation</label>
                    <input type="file" class="form-control" id="dmletterfile" name="dmletterfile">
                  </div>
                  <div class="form-group">
                    <label>Client Validation</label>
                    <select class="custom-select rounded-0" name="svalidation">
                      <option>Physical</option>
                      <option>Telephonic</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary sbtn">Submit</button>
          </div>
        </div>
      </form> 
    </div>     
  </section>
  
  
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type='text/javascript'>
$(document).on('ready',function(){
  $("#ctypenew").click();
});

$('.ctype').on('change', function c() {
// var ctype = document.getElementById("ctype").value;
var ctype=$(this).val();
if(ctype == 'New Client'){
  $(".newclientstatus").show();
  $(".onboardclientstatus").hide();
} else {
  $(".newclientstatus").hide();
  $(".onboardclientstatus").show();
}
$.ajax({
url:'<?=base_url();?>Menu/getctot',
type: "POST",
data: {
ctype: ctype
},
cache: false,
success: function a(result){
$("#task_type").html(result);
}
});
});



$('#pcode').on('change', function a() {
var pcode = document.getElementById("pcode").value;
$.ajax({
url:'<?=base_url();?>Menu/getcinfo',
type: "POST",
data: {
pcode: pcode
},
cache: false,
success: function b(result){
$("#cinfo").html(result);
}
});
});



$('#cid').on('change', function a() {
  var cid=$(this).val();
$.ajax({
url:'<?=base_url();?>Menu/getclientdetail',
type: "POST",
data: {
cid: cid
},
cache: false,
success: function b(result){
  var res=JSON.parse(result);
$("#clientdetail").html(res[0]);
$('#cname').val(res[1]);
var cname = document.getElementById("cname").value;
$.ajax({
url:'<?=base_url();?>Menu/getpcode',
type: "POST",
data: {
cname: cname
},
cache: false,
success: function b(result){
$("#pcode").html(result);
}
});
}
});



});

$('#task_type').on('change', function() {
  $('#noschool').attr('required',false);
  $('#filname').attr('required',false);
   var ab = this.value;
   if(ab=="Report" || ab=="RTTP" || ab=="DIY" || ab=="MnE"){
    $("#test1").show();
    $("#test2").hide();
    $("#test3").hide();
    $("#test4").hide();
    $("#test5").hide();
    $("#test6").hide();
    $("#hidelocation").hide();
   }
   if(ab=="New Client Report"){
    $("#test1").hide();
    $("#test2").show();
    $("#test3").hide();
    $("#test4").hide();
    $("#test5").hide();
    $("#test6").hide();
    $("#hidelocation").hide();
   }
   if(ab=="New client school visit"){
    $("#test1").hide();
    $("#test2").show();
    $("#test3").hide();
    $("#test4").hide();
    $("#test5").hide();
    $("#test6").hide();
    $("#hidelocation").hide();
   }
   if(ab=="OnBoardVisit"){
    $("#test1").hide();
    $("#test2").hide();
    $("#test3").show();
    $("#test4").hide();
    $("#test5").hide();
    $("#test6").hide();
    $("#hidelocation").hide();
   }
   if(ab=="Demo"){
    $("#test1").hide();
    $("#test2").show();
    $("#test3").hide();
    $("#test4").hide();
    $("#test5").hide();
    $("#test6").hide();
    $("#hidelocation").hide();
   }
   if(ab=="Inauguration"){
    $("#test1").hide();
    $("#test2").hide();
    $("#test3").hide();
    $("#test4").hide();
    $("#test5").show();
    $("#test6").hide();
    $("#hidelocation").hide();
   }
   if(ab=="School Identification"){
    $("#test1").hide();
    $("#test2").hide();
    $("#test3").hide();
    $("#test4").hide();
    $("#test5").hide();
    $("#test6").show();
    $("#hidelocation").show();
    $('#noschool').attr('required','required');
    
    //$('#filname').attr('required','required');
   }
});

$("#sletter").on('change',function(){
  if($(this).val()=='Yes'){
    $("#sletterattach").show();
    $('#sletterfile').attr('required','required');
  } else {
    $("#sletterattach").hide();
    $('#sletterfile').attr('required',false);
  }
});
$("#dmletter").on('change',function(){
  if($(this).val()=='Yes'){
    $("#dmletterattach").show();
    $('#dmletterfile').attr('required','required');
  } else {
    $("#dmletterattach").hide();
    $('#dmletterfile').attr('required',false);
  }
});









// Tanvi


var selectedValue = $('#nooflocation').val();
$('#task_type,#nooflocation').change(function(){
  selectedValue = $('#nooflocation').val();
  if(selectedValue == 1){
  // $('.plus-icon').hide();
  $('.multilocation').hide();
  $('.hideon').show();
  $('#noschoolbylocation').prop('required', false);
  $('#location_n').prop('required', false);
}else{
  $('.hideon').hide();
  $('.multilocation').show();
  $('.plus-icon').show();
  $('.delete-input').show();
  $('#noschoolbylocation').prop('required', true);
  $('#location_n').prop('required', true);
}

});


$('.plus-icon').click(function() {
    $('.more').append('<div class="row rremove">'+
                          '<div class="col-6">'+
                            '<div class="form-group">'+
                              '<label for="noschool">NO of School</label>'+
                              '<input type="number" class="form-control" name="noschoolbylocation[]" id="noschoolbylocation" placeholder="NO of School">'+
                            '</div>'+
                          '</div>'+
                          '<div class="col-6">'+
                            '<div class="form-group">'+
                              '<label for="location">Location</label>'+
                              '<div class="input-group">'+
                                  '<textarea class="form-control" name="location_n[]" id="location" placeholder="Location" required></textarea>'+
                                  '<div class="input-group-append">'+
                                    '<span class="input-group-text delete-input"><i class="fas fa-minus"></i></span>'+
                                  '</div>'+
                                '</div>'+
                              '</div>'+
                            '</div>'+
                          '</div>'+
                        '</div>'
                      );
           
});



$(document).on('input', '[name="noschoolbylocation[]"]', function() {
    
  var totalSchools = 0;
    // Loop through all inputs with name "noschoolbylocation[]"
    $('[name="noschoolbylocation[]"]').each(function() {
        // Parse input value as integer and add it to totalSchools
        var value = parseInt($(this).val());
        if (!isNaN(value)) {
            totalSchools += value;
        }
    });

    if((totalSchools > $('#noschool').val()) || (totalSchools < $('#noschool').val())){
      $('.sbtn').hide();
      alert("Entered schools are not equal to Total number of schools.");
    }else {
      $('.sbtn').show();
    }
    // Output total number of schools
    console.log('Total number of schools:', totalSchools);
});
$(document).on('click', '.delete-input', function() {
  $(this).closest('.rremove').remove();
  $rowToRemove.find('input[type="number"]').val(''); // Clear the value of number input
    $rowToRemove.find('textarea').val(''); // Clear the value of textarea
    $rowToRemove.remove(); 
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

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>
</body>
</html>
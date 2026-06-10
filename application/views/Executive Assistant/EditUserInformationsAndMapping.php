<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration | STEM APP | WebAPP</title>
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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
    .scrollme {overflow-x: auto;}.card-title {font-size: 1.6rem;font-weight: 600;}.card.bxs{box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;background: floralwhite;}select.mutlipleuser {height: 300px!important;}label {color: purple;}.input-switch{display: none;}.label-switch{display: inline-block;position: relative;}.label-switch::before, .label-switch::after{content: "";display: inline-block;cursor: pointer;transition: all 0.5s;}.label-switch::before {width: 4em;height: 1em;border: 1px solid #757575;border-radius: 4em;background: #888888;}.label-switch::after {position: absolute;left: 0;top: -8%;width: 1.5em;height: 1.5em;border: 1px solid #757575;border-radius: 4em;background: #ffffff;}.input-switch:checked ~ .label-switch::before {background: #00a900;border-color: #008e00;}.input-switch:checked ~ .label-switch::after {left: unset;right: 0;background: #00ce00;border-color: #009a00;}.info-text {display: inline-block;}.info-text::before{content: "Not active";}.input-switch:checked ~ .info-text::before{content: "Active";}.info-text-message::before{content: "Do you want to change the organization's mapping?";}.input-switch:checked ~ .info-text-message::before{content: "Now you can change the organization's mapping.";}

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
                <!-- <h1 class="m-0">New Companies</h1> -->
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="container">
              <?php
                if ($this->session->flashdata('success_message')): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <?php endif; ?>
              <?php
                if ($this->session->flashdata('error_message')): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <?php endif; ?>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="card text-center">
                  <div class="card-header">
                    <h3 style="color: #2400fa;text-transform: uppercase;">Edit User</h3>
                  </div>
                </div>
              </div>
            </div>
            <?php // dd($userDetails);
              $userData = $userDetails[0];
              
              ?>
            <div class="card p-2 m-2">
              <form id="regForm" method="POST" action="<?=base_url()."Menu/UpdateUserOnDatabase"?>" autocomplete="off">
                <div class="card text-center bxs">
                  <h5 style="color:#ff006f;text-transform: uppercase;">User Information </h5>
                  <center><hr style="width: 350px;"></center>
                  <div class="text-right">
                    <input class='input-switch' type="checkbox" name="user_status" id="demoCheckbox"/>
                    <label class="label-switch" for="demoCheckbox"></label>
                    <span class="info-text"></span>
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label class="form-label" for="firstname"> Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="firstname" name="userfullname" placeholder="Enter User Name" value="<?=$userData->name;?>" required>
                    <input type="hidden" class="form-control" id="targetuid" name="targetuid" value="<?=$targetuid;?>" required>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="inputEmail4">Email <span style="color: red;">*</span></label> 
                    <input type="email" class="form-control" name="email" id="inputEmail4" value="<?=$userData->email;?>" placeholder="Email" required>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="inputPhone">Phone Number <span style="color: red;">*</span></label> 
                    <input type="tel" class="form-control" id="inputPhone" name="phone" value="<?=$userData->phoneno;?>" placeholder="Phone Number" required pattern="[0-9]{10}" title="Please enter a 10-digit phone number" />
                  </div>
                  <div class="form-group col-md-3">
                    <label for="inputPhone">Date Of Birth <span style="color: red;">*</span></label> 
                    <input type="text" class="form-control" id="DtYrDatepicker" value="<?=$userData->dob;?>" placeholder="Date of Birth" name="dob" required  title="Please Select Date" />
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="exampleFormControlSelect1">Country <span style="color: red;">*</span></label>
                    <select class="form-control" name="country" id="exampleFormControlSelect1" required>
                      <?php foreach($userscountrys as $userscountry) { ?>
                      <option value="<?= $userscountry->name ?>">
                        <?= $userscountry->name ?>
                      </option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="userslsctstate">State <span style="color: red;">*</span></label>
                    <select class="form-control" name="state" id="userslsctstate" required>
                      <option>Select State</option>
                      <?php foreach($usersstates as $usersstate) {
                        $selected = ''; 
                        if($userData->state == $usersstate->state_title) {
                            $selected = 'selected'; 
                        }
                         ?>
                      <option <?= $selected ?> value="<?= $usersstate->state_title ?>">
                        <?= $usersstate->state_title ?>
                      </option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="exampleFormControlSelect1">District <span style="color: red;">*</span></label>
                    <select class="form-control" name="district" id="select_district" required>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="exampleFormControlSelect1">City <span style="color: red;">*</span></label>
                    <select class="form-control" name="city" id="select_city" required>
                    </select>
                  </div>
                  <div class="form-group col-md-12">
                    <label for="inputAddress2">Address <span style="color: red;">*</span></label>
                    <textarea class="form-control" name="address" id="exampleFormControlTextarea1" rows="3" required><?=$userData->address;?></textarea>
                  </div>
                </div>
                <div class="card text-center bxs">
                  <h5 style="color:#ff006f;text-transform: uppercase;">User Organizations Mapping </h5>
                  <center><hr style="width: 350px;"></center>
                  <div class="text-center">
                    <input class='input-switch' type="checkbox" name="user_want_to_change_mapping" id="demoCheckbox1"/>
                    <label class="label-switch" for="demoCheckbox1"></label><br>
                    <span class="info-text-message"></span>
                  </div>
                </div>
                <hr>

                <div class="card p-2" id="organtionsmapping">
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="zone" class="form-label">Zone<span style="color: red;">*</span></label>
                    <select class="select form-control" id="zone" name="zone" required>
                      <option value="" disabled selected>Choose zone</option>
                      <?php foreach($zone as $val) {
                        $selected = ''; 
                        if($userData->zone_id == $val->id) {
                            $selected = 'selected'; 
                        }
                        ?>
                      <option <?= $selected ?> value="<?= $val->id ?>">
                        <?= $val->name ?>
                      </option>
                      <?php } ?>
                    </select>
                    <small id="slsct_userzone_text"></small>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="type" class="form-label">User Type<span style="color: red;">*</span></label>
                    <select class="select form-control" id="slsct_usertype" name="type_id" required>
                      <option value="" disabled selected>Choose User Type</option>
                      <?php foreach($allusertypes as $vausertypesl){
                        if($vausertypesl->id ==1 || $vausertypesl->id ==9){continue;}
                        if($vausertypesl->type_status ==0){continue;}
                        
                        $selected = ''; 
                          if($userData->type_id == $vausertypesl->id) {
                              $selected = 'selected'; 
                          }
                        ?>
                      <option <?=$selected?> value="<?=$vausertypesl->id?>"><?=$vausertypesl->name?></option>
                      <?php }?>
                    </select>
                    <small id="slsct_usertype_text"></small>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="type" class="form-label">Admin<span style="color: red;">*</span></label>
                    <select class="select form-control" id="useradmin" name="admin_id" required>
                      <option value="" disabled selected>Choose Admin</option>
                      <?php foreach($usersadmins as $usersadmin){
                        $selected = ''; 
                        if($userData->admin_id == $usersadmin->user_id) {
                            $selected = 'selected'; 
                        }
                        
                        ?>
                      <option <?= $selected; ?> value="<?=$usersadmin->user_id?>"><?=$usersadmin->name?></option>
                      <?php }?>
                    </select>
                    <small id="slsct_useradmin_text"></small>
                  </div>
                </div>
                <hr/>
                <div class="form-row">
                  <div class="form-group col-md-4" id="selectbd_card">
                    <label for="zone" class="form-label"> BD<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" multiple id="selectbd" name="selectbd[]">
                    </select>
                    <small id="selectbd_validation_text"></small> <br>
                    <small id="selectbdselectedCount" style="color:green"></small>
                  </div>
                  <div class="form-group col-md-4" id="clustermanager_card">
                    <label for="zone" class="form-label">Cluster Manager<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" multiple id="clustermanager" name="clustermanager[]">
                    </select>
                    <small id="clustermanager_validation_text"></small><br>
                    <small id="clustermanagerselectedCount" style="color:green"></small>
                  </div>
                  <div class="form-group col-md-4" id="userpst_card">
                    <label for="type" class="form-label">PST<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" multiple id="userpst" name="userpst[]">
                    </select>
                    <small id="userpst_validation_text"></small><br>
                    <small id="userpstselectedCount" style="color:green"></small>
                  </div>
                  <div class="form-group col-md-4" id="sales_coordinator_card">
                    <label for="type" class="form-label">Sales Coordinator<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" id="sales_coordinator" multiple  name="sales_coordinator[]">
                    </select>
                    <small id="sales_coordinator_validation_text"></small><br>
                    <small id="sales_coordinatorselectedCount" style="color:green"></small>
                  </div>
                </div>
            </div>


                <hr/>
                <center><button type="submit" class="btn btn-primary">Update</button></center>
              </form>
            </div>
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
    <!-- jQuery -->
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
   
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
  $(document).ready(function () {

    var userstaus = '<?=$userData->status;?>';
    if(userstaus == 'active'){
        $('#demoCheckbox').prop('checked', true);
    }else{
        $('#demoCheckbox').prop('checked', false);
    }

    $('#demoCheckbox').on('change', function() {
        if ($(this).is(':checked')) {
            console.log('Checkbox is checked');
        } else {
            console.log('Checkbox is not checked');
        }
    });
    $("#organtionsmapping").hide();
    $('#demoCheckbox1').on('change', function() {
        if ($(this).is(':checked')) {
            $("#organtionsmapping").show();
        } else {
            $("#organtionsmapping").hide();
        }
    });


    // info-text-message

var userslsctstate = $('#userslsctstate').val();
$.ajax({
  url: '<?=base_url();?>Menu/GetDistrictINState',
  type: "POST",
  data: {
    userslsctstate: userslsctstate,
  },
  cache: false,
  success: function a(result) {
    var cdistrict = '<?=$userData->district;?>';
    $('#select_district').html(result);
    $('#select_district').val(cdistrict);
    var selectdistrict = $('#select_district').val();
    $.ajax({
      url: '<?=base_url();?>Menu/GetCityInDistrict',
      type: "POST",
      data: {
        selectdistrict: selectdistrict
      },
      cache: false,
      success: function a(result) {
        var cccity = '<?=$userData->city;?>';
        $('#select_city').html(result);
        $('#select_city').val(cccity);
      }
    });
  }
});


var currentDate = new Date(); // Get the current date
var currentYear = currentDate.getFullYear(); // Get the current year dynamically
var currentDateFormatted = currentDate.toISOString().split('T')[0]; // Format the current date as YYYY-MM-DD
$("#DtYrDatepicker").datepicker({
  changeMonth: true,
  changeYear: true,
  showAnim: "fold",
  yearRange: "1920:" + currentYear, // Set the year range from 1920 to the current year
  maxDate: currentDateFormatted, // Set the max date to the current date (in YYYY-MM-DD format)
});


$("#clustermanager").on("change", function () {
  const selectedOptions = $(this).val();
  var check_type_id = $('#slsct_usertype').val();
  if (check_type_id == 3) {
    if (selectedOptions.length > 1) {
      // Allow only the first selected option and deselect the rest
      $(this).val([selectedOptions[0]]);
      alert("You can only select one Cluster Manager.");
    }
  }
});
$("#userpst").on("change", function () {
  const selectedOptions = $(this).val();
  var check_type_id = $('#slsct_usertype').val();
  if (check_type_id == 3 || check_type_id == 13) {
    if (selectedOptions.length > 1) {
      // Allow only the first selected option and deselect the rest
      $(this).val([selectedOptions[0]]);
      alert("You can only select one PST.");
    }
  }
});
$("#sales_coordinator").on("change", function () {
  const selectedOptions = $(this).val();
  var check_type_id = $('#slsct_usertype').val();
  if (check_type_id == 3 || check_type_id == 13 || check_type_id == 4) {
    if (selectedOptions.length > 1) {
      // Allow only the first selected option and deselect the rest
      $(this).val([selectedOptions[0]]);
      alert("* You can only select one Sales Coordinator.");
    }
  }
});


$('#selectbd_card').hide();
$('#clustermanager_card').hide();
$('#userpst_card').hide();
$('#sales_coordinator_card').hide();
var currentDate = new Date().toISOString().split('T')[0];
$("#inputDate").attr("max", currentDate);

var useradmin = $('#useradmin').val();
var zoneid = $('#zone').val();
var slsct_usertype = $('#slsct_usertype').val();

$.ajax({
  url: '<?=base_url();?>Menu/GetUserMappingFiled',
  type: "POST",
  data: {
    zoneid: zoneid,
    slsct_usertype: slsct_usertype,
    useradmin: useradmin
  },
  cache: false,
  success: function a(jsonDataString) {
    const jsonData = JSON.parse(jsonDataString);

    function populateSelect(typeId, selectId) {
      const filteredData = jsonData.filter(user => user.type_id === typeId);
      const $select = $(`#${selectId}`);

      filteredData.forEach(user => {
        $select.append(`<option value="${user.user_id}">${user.name}</option>`);
      });
    }
    $("#selectbd,#userpst,#sales_coordinator,#clustermanager").html("");
    // Populate each select element
    populateSelect("3", "selectbd");
    populateSelect("4", "userpst");
    populateSelect("15", "sales_coordinator");
    populateSelect("13", "clustermanager");

    $("#selectbd option").css("color", "red"); // Change to your desired color
    $("#userpst option").css("color", "blue"); // Change to your desired color
    $("#sales_coordinator option").css("color", "green"); // Change to your desired color
    $("#clustermanager option").css("color", "purple"); // Change to your desired color

    var totalselectbdOptions = $('#selectbd option').length;
    $("#selectbd_validation_text").text(' Total BD : ' + totalselectbdOptions).css('color', 'green');

    var totaluserpstOptions = $('#userpst option').length;
    $("#userpst_validation_text").text(' Total PST : ' + totaluserpstOptions).css('color', 'green');

    var totaluserclusterOptions = $('#clustermanager option').length;
    $("#clustermanager_validation_text").text(' Total Cluster : ' + totaluserclusterOptions).css('color', 'green');

    var totaluserscOptions = $('#sales_coordinator option').length;
    $("#sales_coordinator_validation_text").text(' Total Sales Coordinator : ' + totaluserscOptions).css('color', 'green');


    $.ajax({
      url: '<?=base_url();?>Menu/GetUserMappingFiledBySelectedRole',
      type: "POST",
      data: {
        targetuid: '<?=$targetuid?>',
      },
      cache: false, // Correct placement
      success: function (jsonDataStrings) {

        $('#userpst option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchPST = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "4");
          if (userMatchPST) {
            $(this).prop('selected', true);
          }
        });

        $('#selectbd option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchSelectBD = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "3");
          if (userMatchSelectBD) {
            $(this).prop('selected', true);
          }
        });

        $('#clustermanager option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchSelectCM = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "13");
          if (userMatchSelectCM) {
            $(this).prop('selected', true);
          }
        });

        $('#sales_coordinator option').each(function () {
          const optionValuesc = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchSelectSC = jsonDatas.some(user => user.user_id.toString() === optionValuesc && user.type_id === "15");
          if (userMatchSelectSC) {
            $(this).prop('selected', true);
          }else{
            console.log("user not matched for sc",userMatchSelectSC );
          }
        });
      }
    });


  }
});


if (slsct_usertype == 3) {
  $('#selectbd_card').hide();
  $('#clustermanager_card').show();
  $('#userpst_card').show();
  $('#sales_coordinator_card').show();

} else if (slsct_usertype == 13) {

  $('#clustermanager_card').hide();
  $('#selectbd_card').show();
  $('#userpst_card').show();
  $('#sales_coordinator_card').show();

} else if (slsct_usertype == 4) {
  $('#clustermanager_card').show();
  $('#selectbd_card').show();
  $('#userpst_card').hide();
  $('#sales_coordinator_card').show();

} else if (slsct_usertype == 15) {
  $('#clustermanager_card').show();
  $('#selectbd_card').show();
  $('#userpst_card').show();
  $('#sales_coordinator_card').hide();
} else {
  $('#clustermanager_card').show();
  $('#selectbd_card').show();
  $('#userpst_card').show();
  $('#sales_coordinator_card').show();
}



});

$('#userslsctstate').on('change', function () {
var userslsctstate = $(this).val();
$.ajax({
  url: '<?=base_url();?>Menu/GetDistrictINState',
  type: "POST",
  data: {
    userslsctstate: userslsctstate
  },
  cache: false,
  success: function a(result) {
    $('#select_district').html(result);
  }
});
});
$('#select_district').on('change', function () {
var selectdistrict = $(this).val();
$.ajax({
  url: '<?=base_url();?>Menu/GetCityInDistrict',
  type: "POST",
  data: {
    selectdistrict: selectdistrict
  },
  cache: false,
  success: function a(result) {
    $('#select_city').html(result);
  }
});
});


$('#zone').on('change', function () {
var zoneid = $(this).val();
$.ajax({
  url: '<?=base_url();?>Menu/GetAdminByZONEID',
  type: "POST",
  data: {
    zoneid: zoneid
  },
  cache: false,
  success: function a(result) {
    // $('#select_city').html(result);
    // console.log(result);
  }
});
});


$('#zone,#useradmin,#slsct_usertype').on('change', function () {


function isEmpty(value) {
  return value === null || value === undefined || value.trim() === '';
}

var useradmin = $('#useradmin').val();
var zoneid = $('#zone').val();
var slsct_usertype = $('#slsct_usertype').val();

var changedId = $(this).attr('id');
if (changedId == 'zone' && (isEmpty(slsct_usertype) || isEmpty(useradmin))) {
  if (isEmpty(slsct_usertype)) {
    $("#slsct_usertype_text").text(" * Please Select User Type").css("color", "red");
  } else if (isEmpty(useradmin)) {
    $("#slsct_useradmin_text").text(" * Please Select User Admin").css("color", "red");
  }
} else if (changedId == 'slsct_usertype' && (isEmpty(zoneid) || isEmpty(useradmin))) {
  if (isEmpty(zoneid)) {
    alert(" * Please Select Zone");
    $("#slsct_userzone_text").text(" * Please Select User Zone").css("color", "red");
  } else if (isEmpty(useradmin)) {
    $("#slsct_useradmin_text").text(" * Please Select User Admin").css("color", "red");
  }
} else if (changedId == 'useradmin' && (isEmpty(zoneid) || isEmpty(slsct_usertype))) {
  if (isEmpty(zoneid)) {
    $("#slsct_userzone_text").text(" * Please Select User Zone").css("color", "red");
  } else if (isEmpty(slsct_usertype)) {
    $("#slsct_usertype_text").text(" * Please Select User Type").css("color", "red");
  }
} else {

  $("#slsct_userzone_text, #slsct_usertype_text, #slsct_useradmin_text").text(" * Good JOB!").css("color", "green");

  $.ajax({
    url: '<?=base_url();?>Menu/GetUserMappingFiled',
    type: "POST",
    data: {
      zoneid: zoneid,
      slsct_usertype: slsct_usertype,
      useradmin: useradmin
    },
    cache: false,
    success: function a(jsonDataString) {
      const jsonData = JSON.parse(jsonDataString);

      function populateSelect(typeId, selectId) {
        const filteredData = jsonData.filter(user => user.type_id === typeId);
        const $select = $(`#${selectId}`);

        filteredData.forEach(user => {
          $select.append(`<option value="${user.user_id}">${user.name}</option>`);
        });
      }
      $("#selectbd,#userpst,#sales_coordinator,#clustermanager").html("");
      // Populate each select element
      populateSelect("3", "selectbd");
      populateSelect("4", "userpst");
      populateSelect("15", "sales_coordinator");
      populateSelect("13", "clustermanager");

      $("#selectbd option").css("color", "red"); // Change to your desired color
      $("#userpst option").css("color", "blue"); // Change to your desired color
      $("#sales_coordinator option").css("color", "green"); // Change to your desired color
      $("#clustermanager option").css("color", "purple"); // Change to your desired color

      var totalselectbdOptions = $('#selectbd option').length;
      $("#selectbd_validation_text").text(' Total BD : ' + totalselectbdOptions).css('color', 'green');

      var totaluserpstOptions = $('#userpst option').length;
      $("#userpst_validation_text").text(' Total PST : ' + totaluserpstOptions).css('color', 'green');

      var totaluserclusterOptions = $('#clustermanager option').length;
      $("#clustermanager_validation_text").text(' Total Cluster : ' + totaluserclusterOptions).css('color', 'green');

      var totaluserscOptions = $('#sales_coordinator option').length;
      $("#sales_coordinator_validation_text").text(' Total Sales Coordinator : ' + totaluserscOptions).css('color', 'green');

      $.ajax({
      url: '<?=base_url();?>Menu/GetUserMappingFiledBySelectedRole',
      type: "POST",
      data: {
        targetuid: '<?=$targetuid?>',
      },
      cache: false, // Correct placement
      success: function (jsonDataStrings) {

        $('#userpst option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchPST = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "4");
          if (userMatchPST) {
            $(this).prop('selected', true);
          }
        });

        $('#selectbd option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchSelectBD = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "3");
          if (userMatchSelectBD) {
            $(this).prop('selected', true);
          }
        });

        $('#clustermanager option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchSelectCM = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "13");
          if (userMatchSelectCM) {
            $(this).prop('selected', true);
          }
        });

        $('#sales_coordinator option').each(function () {
          const optionValue = $(this).val();
          const jsonDatas = JSON.parse(jsonDataStrings);
          const userMatchSelectSC = jsonDatas.some(user => user.user_id.toString() === optionValue && user.type_id === "15");
          if (userMatchSelectSC) {
            $(this).prop('selected', true);
          }
        });
      }
    });

    }
  });


  if (slsct_usertype == 3) {
    $('#selectbd_card').hide();
    $('#clustermanager_card').show();
    $('#userpst_card').show();
    $('#sales_coordinator_card').show();

  } else if (slsct_usertype == 13) {

    $('#clustermanager_card').hide();
    $('#selectbd_card').show();
    $('#userpst_card').show();
    $('#sales_coordinator_card').show();

  } else if (slsct_usertype == 4) {
    $('#clustermanager_card').show();
    $('#selectbd_card').show();
    $('#userpst_card').hide();
    $('#sales_coordinator_card').show();

  } else if (slsct_usertype == 15) {
    $('#clustermanager_card').show();
    $('#selectbd_card').show();
    $('#userpst_card').show();
    $('#sales_coordinator_card').hide();
  } else {
    $('#clustermanager_card').show();
    $('#selectbd_card').show();
    $('#userpst_card').show();
    $('#sales_coordinator_card').show();
  }
}


});


$(document).ready(function () {


    $('#selectbd').on('change', function () {
    const selectbdselectedCount = $(this).find('option:selected').length;
    $('#selectbdselectedCount').text("Selected User is: "+selectbdselectedCount);
  });

    $('#clustermanager').on('change', function () {
    const clustermanagerselectedCount = $(this).find('option:selected').length;
    $('#clustermanagerselectedCount').text("Selected User is: "+clustermanagerselectedCount);
  });

    $('#userpst').on('change', function () {
    const userpstselectedCount = $(this).find('option:selected').length;
    $('#userpstselectedCount').text("Selected User is: "+userpstselectedCount);
  });
  
    $('#sales_coordinator').on('change', function () {
    const sales_coordinatorselectedCount = $(this).find('option:selected').length;
    $('#sales_coordinatorselectedCount').text("Selected User is: "+sales_coordinatorselectedCount);
  });

    // var selectbdselectedCount = $('#selectbd').find('option:selected').length;
    // $('#selectbdselectedCount').text("Selected User is: "+selectbdselectedCount);

    // var clustermanagerselectedCount = $('#clustermanager').find('option:selected').length;
    // $('#clustermanagerselectedCount').text("Selected User is: "+clustermanagerselectedCount);

    // var userpstselectedCount = $('#userpst').find('option:selected').length;
    // $('#userpstselectedCount').text("Selected User is: "+userpstselectedCount);

    // var sales_coordinatorselectedCount = $('#sales_coordinator').find('option:selected').length;
    // $('#sales_coordinatorselectedCount').text("Selected User is: "+sales_coordinatorselectedCount);

    // function updateSelectedCount() {
    //     var clustermanagerselectedCount = $('#clustermanager').find('option:selected').length;
    //     $('#clustermanagerselectedCount').text("Selected User(s): " + clustermanagerselectedCount);
    // }

    // Initialize the selected count on page load
    // updateSelectedCount();

   


});
          
          
    </script>
  </body>
</html>
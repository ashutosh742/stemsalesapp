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
      .scrollme {
      overflow-x: auto;
      }
      .card-title {
      font-size: 1.6rem;
      font-weight: 600;
      }
      .card.bxs{box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;}
      select.mutlipleuser {
      height: 300px!important;
      }
      label {color: purple;}
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
                    <h3 style="color: #2400fa;text-transform: uppercase;">User Registration</h3>
                    <span>Go to <a href="<?=base_url().'/Menu/UserDisplayPage'?>" target="_BLANK">User Details Page</a></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card p-2 m-2">
              <form id="regForm" method="POST" action="<?=base_url()."Menu/StoreUserOnDatabase"?>" autocomplete="off">
                <div class="card text-center bxs">
                  <h5 style="color:#ff006f;text-transform: uppercase;">User Information </h5>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label class="form-label" for="firstname"> First Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter User First Name" required>
                  </div>
                  <div class="form-group col-md-3">
                    <label class="form-label" for="lastname"> Last Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter User Last Name" required>
                  </div>
                  <div class="form-group col-md-3">
                    <label class="form-label" for="usernametype"> User Name <span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="usernametype" name="usernametype" placeholder="Enter Username" required>
                    <small id="checkusernametext"></small> <br>
                    <small id="checkusernametext_length"></small>
                  </div>

                  <div class="form-group col-md-3">
                    <label class="form-label" for="usernametype"> User Cluster Zone <span style="color: red;">*</span></label>
                    <?php $userClusterZones = $this->Menu_model->GetUserClusterZone(); ?>
                    <select class="form-control" name="cluster_zone_name" required>
                      <option value="" disabled selected> -- Select --</option>
                      <?php foreach($userClusterZones as $userClusterZone) { ?>
                      <option value="<?= $userClusterZone->cluster_zone_name; ?>">
                        <?= $userClusterZone->cluster_zone_name; ?>
                      </option>
                      <?php } ?>
                    </select>
                  </div>
                 
                </div>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="inputEmail4">Email <span style="color: red;">*</span></label> 
                    <input type="email" class="form-control" name="email" id="inputEmail4" placeholder="Email" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="inputPhone">Phone Number <span style="color: red;">*</span></label> 
                    <input type="tel" class="form-control" id="inputPhone" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}" title="Please enter a 10-digit phone number" />
                  </div>
                  <div class="form-group col-md-4">
                    <label for="inputPhone">Date Of Birth <span style="color: red;">*</span></label> 
                    <input type="text" class="form-control" id="DtYrDatepicker" placeholder="Date of Birth" name="dob" required  title="Please Select Date" />
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
                      <?php foreach($usersstates as $usersstate) { ?>
                      <option value="<?= $usersstate->state_title ?>">
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
                    <textarea class="form-control" name="address" id="exampleFormControlTextarea1" rows="3" required></textarea>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="password" class="form-label">Password<span style="color: red;">*</span></label>
                    <div class="input-group">
                      <input type="password" autocomplete="off"  class="form-control" name="password" id="password" required/>
                      <span class="input-group-text" onclick="togglePassword('password')">
                      <i class="fas fa-eye" id="togglePasswordIcon"></i>
                      </span>
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="confirmPassword" class="form-label">Confirm Password<span style="color: red;">*</span></label>
                    <div class="input-group">
                      <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" required/>
                      <span class="input-group-text" onclick="togglePassword('confirmPassword')">
                      <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                      </span>
                    </div>
                  </div>
                  <div class="form-group col-md-12">
                    <center><small id="password_confirmPassword"></small></center>
                  </div>
                  
                </div>
                <div class="card text-center bxs">
                  <h5 style="color:#ff006f;text-transform: uppercase;">User Organizations Mapping </h5>
                </div>
                <hr>
                <div class="form-row">
                  <div class="form-group col-md-3">
                    <label for="zone" class="form-label">Zone<span style="color: red;">*</span></label>
                    <select class="select form-control" id="zone" name="zone" required>
                      <option value="" disabled selected>Choose zone</option>
                      <?php foreach($zone as $val) { ?>
                      <option value="<?= $val->id ?>">
                        <?= $val->name ?>
                      </option>
                      <?php } ?>
                    </select>
                    
                    <small id="slsct_userzone_text"></small>
                  </div>


                  <div class="form-group col-md-3">
                    <label for="type" class="form-label">User Type<span style="color: red;">*</span></label>
                    <select class="select form-control" id="slsct_usertype" name="type_id" required>
                      <option value="" disabled selected>Choose User Type</option>
                      <?php foreach($allusertypes as $vausertypesl){
                        if($vausertypesl->id ==1 || $vausertypesl->id ==9){continue;}
                        if($vausertypesl->type_status ==0){continue;}
                        ?>
                      <option value="<?=$vausertypesl->id?>"><?=$vausertypesl->name?></option>
                      <?php }?>
                    </select>
                    <small id="slsct_usertype_text"></small>
                  </div>


                  <div class="form-group col-md-3" id="slsct_inside_sales_card">
                    <label for="type" class="form-label">Select Inside Sales</label>
                    <select class="select form-control" id="slsct_inside_sales" name="inside_sales">
                      <option value="no">NO</option>
                      <option value="yes">Yes</option>
                    </select>
                  </div>


                  <div class="form-group col-md-3">
                    <label for="type" class="form-label">Admin<span style="color: red;">*</span></label>
                    <select class="select form-control" id="useradmin" name="admin_id" required>
                      <option value="" disabled selected>Choose Admin</option>
                      <?php foreach($usersadmins as $usersadmin){?>
                      <option value="<?=$usersadmin->user_id?>"><?=$usersadmin->name?></option>
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
                    <small id="selectbd_validation_text"></small><br>
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


                    <div class="form-group col-md-4" id="assistant_sales_head_card">
                    <label for="type" class="form-label">Assistant Sales Head<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" id="assistant_sales_head" multiple  name="assistant_sales_head[]">
                    </select>
                    <small id="assistant_sales_head_validation_text"></small><br>
                    <small id="assistant_sales_headselectedCount" style="color:green"></small>
                  </div>

                   <div class="form-group col-md-4" id="regional_manager_card">
                    <label for="type" class="form-label">Regional Manager<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" id="regional_manager" multiple  name="regional_manager[]">
                    </select>
                    <small id="regional_manager_validation_text"></small><br>
                    <small id="regional_managerselectedCount" style="color:green"></small>
                  </div>
                   <div class="form-group col-md-4" id="assistant_cluster_manager_card">
                    <label for="type" class="form-label">Assistant Cluster Manager<span style="color: red;">*</span></label>
                    <select class="select form-control mutlipleuser" id="assistant_cluster_manager" multiple  name="assistant_cluster_manager[]">
                    </select>
                    <small id="assistant_cluster_manager_validation_text"></small><br>
                    <small id="assistant_cluster_managerselectedCount" style="color:green"></small>
                  </div>
              

                </div>
                <hr/>
                <center><button type="submit" class="btn btn-primary">Register</button></center>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <script>
      $(document).ready(function() {

        $("#slsct_inside_sales_card").hide();

        $('#slsct_usertype').on('change', function() {

            var slsct_usertype_val = $(this).val();

              if(slsct_usertype_val ==3){
                $("#slsct_inside_sales_card").show();
              }else{
                $("#slsct_inside_sales_card").hide();
              }
              
        });


   

        var currentDate = new Date(); // Get the current date
        var currentYear = currentDate.getFullYear(); // Get the current year dynamically
        var currentDateFormatted = currentDate.toISOString().split('T')[0]; // Format the current date as YYYY-MM-DD
        $( "#DtYrDatepicker" ).datepicker({
            changeMonth: true,
            changeYear: true,
            showAnim: "fold",
            yearRange: "1920:" + currentYear, // Set the year range from 1920 to the current year
            maxDate: currentDateFormatted, // Set the max date to the current date (in YYYY-MM-DD format)
        });


        $("#clustermanager").on("change", function () {
                const selectedOptions = $(this).val();
                var check_type_id =  $('#slsct_usertype').val();
            if(check_type_id == 3 || check_type_id == 24){
                        if (selectedOptions.length > 1) {
                        // Allow only the first selected option and deselect the rest
                        $(this).val([selectedOptions[0]]);
                        alert("You can only select one Cluster Manager.");
                    }
            }
        });
        $("#userpst").on("change", function () {
                const selectedOptions = $(this).val();
                var check_type_id =  $('#slsct_usertype').val();
            if(check_type_id == 3 || check_type_id == 13 || check_type_id == 24){
                        if (selectedOptions.length > 1) {
                        // Allow only the first selected option and deselect the rest
                        $(this).val([selectedOptions[0]]);
                        alert("You can only select one PST.");
                    }
            }
        });
        $("#sales_coordinator").on("change", function () {
                const selectedOptions = $(this).val();
                var check_type_id =  $('#slsct_usertype').val();
            if(check_type_id == 3 || check_type_id == 13 || check_type_id == 4 || check_type_id == 24){
                        if (selectedOptions.length > 1) {
                        // Allow only the first selected option and deselect the rest
                        $(this).val([selectedOptions[0]]);
                        alert("* You can only select one Sales Coordinator.");
                    }
            }
        });


          $("#assistant_sales_head").on("change", function () {
                const selectedOptions = $(this).val();
                var check_type_id =  $('#slsct_usertype').val();
            if(check_type_id == 3 || check_type_id == 13 || check_type_id == 4 || check_type_id == 15 || check_type_id == 19 || check_type_id == 20 || check_type_id == 21 || check_type_id == 22 || check_type_id == 23 || check_type_id == 24){
                        if (selectedOptions.length > 1) {
                        // Allow only the first selected option and deselect the rest
                        $(this).val([selectedOptions[0]]);
                        alert("* You can only select one Assistant Sales Head.");
                    }
            }
        });
         $("#regional_manager").on("change", function () {
                const selectedOptions = $(this).val();
                var check_type_id =  $('#slsct_usertype').val();
            if(check_type_id == 3 || check_type_id == 13 || check_type_id == 4 || check_type_id == 15 || check_type_id == 22 || check_type_id == 23 || check_type_id == 24){
                        if (selectedOptions.length > 1) {
                        // Allow only the first selected option and deselect the rest
                        $(this).val([selectedOptions[0]]);
                        alert("* You can only select one Regional Manager.");
                    }
            }
        });

         $("#assistant_cluster_manager").on("change", function () {
                const selectedOptions = $(this).val();
                var check_type_id =  $('#slsct_usertype').val();
            if(check_type_id == 3){
                        if (selectedOptions.length > 1) {
                        // Allow only the first selected option and deselect the rest
                        $(this).val([selectedOptions[0]]);
                        alert("* You can only select one Assistant Cluster Manager.");
                    }
            }
        });



          $('#selectbd_card').hide();
          $('#clustermanager_card').hide();
          $('#userpst_card').hide();
          $('#sales_coordinator_card').hide();

          $('#assistant_sales_head_card').hide();
          $('#regional_manager_card').hide();
          $('#assistant_cluster_manager_card').hide();

          var currentDate = new Date().toISOString().split('T')[0];
          $("#inputDate").attr("max", currentDate);
      
              $('#firstname, #lastname').on('input', () => {
              const firstname = $('#firstname').val().trim().toLowerCase().split(/\s+/).join('.');
              const lastname = $('#lastname').val().trim().toLowerCase().split(/\s+/).join('.');
              var firstname_lastname = (`${firstname}.${lastname}`).replace(/\.$/, '');
      
              $('#usernametype').val(firstname_lastname);
      
              var usernametypevalue = firstname_lastname;
              $.ajax({
                      url:'<?=base_url();?>Menu/CheckUsernameExistsorNot',
                      type: "POST",
                      data: {
                          usernametypevalue: usernametypevalue
                      },
                      cache: false,
                      success: function a(result){
                          if(result == 1){
                              $("#checkusernametext").text("* Username already exists.");
                              $('#checkusernametext').css('color', 'red');
                          }else{
                              $("#checkusernametext").text("* Good job! Username does not exist.");
                              $('#checkusernametext').css('color', 'green');
                          }
                      }
                      });
      
      
                      var inputLength = usernametypevalue.length;
                      if (inputLength < 6) {
                          $("#checkusernametext_length").text("* Username minimum 6 character.");
                          $('#checkusernametext_length').css('color', 'red');
                      }else{
                          $("#checkusernametext_length").text("");
                      }
              });
      
              $('#usernametype').on('input', () => {
                  var usernametypevalue = $('#usernametype').val().replace(/\.$/, '');
              $.ajax({
                      url:'<?=base_url();?>Menu/CheckUsernameExistsorNot',
                      type: "POST",
                      data: {
                          usernametypevalue: usernametypevalue
                      },
                      cache: false,
                      success: function a(result){
                          if(result == 1){
                              $("#checkusernametext").text("* Username already exists.");
                              $('#checkusernametext').css('color', 'red');
                          }else{
                              $("#checkusernametext").text("* Good job! Username does not exist.");
                              $('#checkusernametext').css('color', 'green');
                          }
                      }
                      });
      
                      var inputLength = usernametypevalue.length;
                      if (inputLength < 6) {
                          $("#checkusernametext_length").text("* Username minimum 6 character.");
                          $('#checkusernametext_length').css('color', 'red');
                      }else{
                          $("#checkusernametext_length").text("");
                      }
              });

              
              $('#confirmPassword').on('input', () => {
                var password = $("#password").val();
                var confirmPassword = $("#confirmPassword").val();
                if(password == ''){
                    $("#password_confirmPassword").text("* Please Enter Password First.").css('color', 'red');
                    $('#password').value("");
                }else if(password !== confirmPassword){
                    $("#password_confirmPassword").text("* Password & Confirm Password Does not Matched.").css('color', 'red');
                }else if(password == confirmPassword){
                    $("#password_confirmPassword").text("* Good JOB! Password & Confirm Password Matched.").css('color', 'green');
                }
               });

            



      });
      
                $('#userslsctstate').on('change', function() {
                  var userslsctstate = $(this).val();
                  $.ajax({
                          url:'<?=base_url();?>Menu/GetDistrictINState',
                          type: "POST",
                          data: {
                              userslsctstate: userslsctstate
                          },
                          cache: false,
                          success: function a(result){
                              $('#select_district').html(result);
                          }
                          });
                  });
                  $('#select_district').on('change', function() {
                  var selectdistrict = $(this).val();
                  $.ajax({
                          url:'<?=base_url();?>Menu/GetCityInDistrict',
                          type: "POST",
                          data: {
                              selectdistrict: selectdistrict
                          },
                          cache: false,
                          success: function a(result){
                              $('#select_city').html(result);
                          }
                          });
                  });
      
      
                  function togglePassword(fieldId) {
                  var passwordField = document.getElementById(fieldId);
                  var toggleIcon = passwordField.nextElementSibling.querySelector('i');
                  
                  if (passwordField.type === 'password') {
                      passwordField.type = 'text';
                      toggleIcon.classList.remove('fa-eye');
                      toggleIcon.classList.add('fa-eye-slash');
                  } else {
                      passwordField.type = 'password';
                      toggleIcon.classList.remove('fa-eye-slash');
                      toggleIcon.classList.add('fa-eye');
                  }
              }
              $('#zone').on('change', function() {
                  var zoneid = $(this).val();
                  $.ajax({
                          url:'<?=base_url();?>Menu/GetAdminByZONEID',
                          type: "POST",
                          data: {
                              zoneid: zoneid
                          },
                          cache: false,
                          success: function a(result){
                              // $('#select_city').html(result);
                              // console.log(result);
                          }
                          });
              });
      
      
      
      
              $('#zone,#useradmin,#slsct_usertype').on('change', function() {
                  
              
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
                      }else if (isEmpty(useradmin)) {
                          $("#slsct_useradmin_text").text(" * Please Select User Admin").css("color", "red");
                      }
                  }else if(changedId == 'slsct_usertype' && (isEmpty(zoneid) || isEmpty(useradmin))){
                      if (isEmpty(zoneid)) {
                          alert(" * Please Select Zone");
                          $("#slsct_userzone_text").text(" * Please Select User Zone").css("color", "red");
                      }else if(isEmpty(useradmin)){
                        $("#slsct_useradmin_text").text(" * Please Select User Admin").css("color", "red");
                      }
                  }else if(changedId == 'useradmin' && (isEmpty(zoneid) || isEmpty(slsct_usertype))){
                      if (isEmpty(zoneid)) {
                        $("#slsct_userzone_text").text(" * Please Select User Zone").css("color", "red");
                      }else if(isEmpty(slsct_usertype)){
                        $("#slsct_usertype_text").text(" * Please Select User Type").css("color", "red");
                      }
                  }
                else {

                    $("#slsct_userzone_text, #slsct_usertype_text, #slsct_useradmin_text").text(" * Good JOB!").css("color", "green");

                        $("#assistant_sales_head").html("");
                        $("#regional_manager").html("");
                        $("#assistant_cluster_manager").html("");


                          $.ajax({
                          url:'<?=base_url();?>Menu/GetUserMappingFiled',
                          type: "POST",
                          data: {
                              zoneid: zoneid,
                              slsct_usertype:slsct_usertype,
                              useradmin:useradmin
                          },
                          cache: false,
                          success: function a(jsonDataString ){
                              const jsonData = JSON.parse(jsonDataString);
                              function populateSelect(typeId, selectId) {
                                  const filteredData = jsonData.filter(user => user.type_id === typeId);
                                  const $select = $(`#${selectId}`);
      
                                  filteredData.forEach(user => {
                                      $select.append(`<option value="${user.user_id}">${user.name} - (${user.user_zone_name})</option>`);
                                  });
                              }
                              $("#selectbd,#userpst,#sales_coordinator,#clustermanager").html("");
                              // Populate each select element


                              populateSelect("3", "selectbd");
                              populateSelect("4", "userpst");
                              populateSelect("15", "sales_coordinator");
                              populateSelect("13", "clustermanager");

                              populateSelect("19", "assistant_sales_head");
                              populateSelect("20", "assistant_sales_head");
                              populateSelect("21", "assistant_sales_head");

                              populateSelect("22", "regional_manager");
                              populateSelect("23", "regional_manager");
                              populateSelect("24", "assistant_cluster_manager");

      
                              $("#selectbd option").css("color", "red"); // Change to your desired color
                              $("#userpst option").css("color", "blue"); // Change to your desired color
                              $("#sales_coordinator option").css("color", "green"); // Change to your desired color
                              $("#clustermanager option").css("color", "purple"); // Change to your desired color
      
                              var totalselectbdOptions = $('#selectbd option').length;
                              $("#selectbd_validation_text").text(' Total BD : '+totalselectbdOptions).css('color', 'green');

                              var totaluserpstOptions = $('#userpst option').length;
                              $("#userpst_validation_text").text(' Total PST : '+totaluserpstOptions).css('color', 'green');

                              var totaluserclusterOptions = $('#clustermanager option').length;
                              $("#clustermanager_validation_text").text(' Total Cluster : '+totaluserclusterOptions).css('color', 'green');

                              var totaluserscOptions = $('#sales_coordinator option').length;
                              $("#sales_coordinator_validation_text").text(' Total Sales Coordinator : '+totaluserscOptions).css('color', 'green');

                              var totaluserashOptions = $('#assistant_sales_head option').length;
                              $("#assistant_sales_head_validation_text").text(' Total Assistant Sales Head : '+totaluserashOptions).css('color', 'green');

                              var totaluserrmOptions = $('#regional_manager option').length;
                              $("#regional_manager_validation_text").text(' Total Regional Manager : '+totaluserrmOptions).css('color', 'green');

                               var totaluseracmOptions = $('#assistant_cluster_manager option').length;
                              $("#assistant_cluster_manager_validation_text").text(' Total Assistant Cluster Manager : '+totaluseracmOptions).css('color', 'green');

      
                          }
                          });
      
      
                          if(slsct_usertype == 3){
                              $('#selectbd_card').hide();
                              $('#clustermanager_card').show();
                              $('#userpst_card').show();
                              $('#sales_coordinator_card').show();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').show();
                              $('#assistant_cluster_manager_card').show();
                              
                          }else if(slsct_usertype == 13){
                              
                              $('#clustermanager_card').hide();
                              $('#selectbd_card').show();
                              $('#userpst_card').show();
                              $('#sales_coordinator_card').show();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').show();
                              $('#assistant_cluster_manager_card').show();
                              
                          }else if(slsct_usertype == 4){
                              $('#clustermanager_card').show();
                              $('#selectbd_card').show();
                              $('#userpst_card').hide();
                              $('#sales_coordinator_card').show();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').show();
                              $('#assistant_cluster_manager_card').show();
      
                          }else if(slsct_usertype == 15){
                              $('#clustermanager_card').show();
                              $('#selectbd_card').show();
                              $('#userpst_card').show();
                              $('#sales_coordinator_card').hide();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').hide();
                              $('#assistant_cluster_manager_card').show();
                          }else if(slsct_usertype == 19 || slsct_usertype == 20 || slsct_usertype == 21){
                              $('#clustermanager_card').show();
                              $('#selectbd_card').show();
                              $('#userpst_card').hide();
                              $('#sales_coordinator_card').show();
                              $('#assistant_sales_head_card').hide();
                              $('#regional_manager_card').show();
                              $('#assistant_cluster_manager_card').show();
                          }else if(slsct_usertype == 22 || slsct_usertype == 23){
                              $('#clustermanager_card').show();
                              $('#selectbd_card').show();
                              $('#userpst_card').hide();
                              $('#sales_coordinator_card').hide();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').hide();
                              $('#assistant_cluster_manager_card').show();
                          }else if(slsct_usertype == 24){
                              $('#clustermanager_card').show();
                              $('#selectbd_card').show();
                              $('#userpst_card').hide();
                              $('#sales_coordinator_card').show();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').show();
                              $('#assistant_cluster_manager_card').hide();
                          }else{
                              $('#clustermanager_card').show();
                              $('#selectbd_card').show();
                              $('#userpst_card').show();
                              $('#sales_coordinator_card').show();
                              $('#assistant_sales_head_card').show();
                              $('#regional_manager_card').show();
                              $('#assistant_cluster_manager_card').show();
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



});
            
      
      
      
      
      
          
    </script>
  </body>
</html>
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
<!-- /.navbar -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
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
<!-- Main content -->
<section class="content">
<div class="container-fluid">
    <form id="regForm" method="POST" action="" autocomplete="off">
            <section class="gradient-custom">
                <div class="container py-5">
                    <div class="row justify-content-center align-items-center ">
                    <div class="col-md-12">
                        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">User Entry Form</h3>

                            <div class="row">
                                <div class="col-md-4 mb-4">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="fname"> First Name <span style="color: red;">*</span></label>
                                    <input type="text" id="fname" name="fname" class="form-control form-control-lg" required />
                                    <input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>" />
                                    
                                </div>

                                </div>

                                <div class="col-md-4 mb-4">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="lname"> Last Name <span style="color: red;">*</span></label>
                                    <input type="text" id="lname" name="lname" class="form-control form-control-lg" required />
                                    <input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>" />
                                    
                                </div>

                                </div>

                                <div class="col-md-4 mb-4">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="email">Email<span style="color: red;">*</span></label>
                                    <input type="text" id="email" name="email" class="form-control form-control-lg" required/>
                                    
                                </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4 d-flex align-items-center">

                                <div data-mdb-input-init class="form-outline datepicker w-100">
                                    <label for="Username" class="form-label">Username<span style="color: red;">*</span></label>
                                    <input type="text" class="form-control form-control-lg" name="Username" id="Username" required/>
                                    
                                </div>

                                </div>

                                <div class="col-md-6 mb-4 align-items-center">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="phone">Phone number</label>
                                    <input type="tel" maxlength="10" class="form-control form-control-lg" name="phone" id="phone" 
                                        value="<?= isset($userDetails) ? $userDetails[0]->phoneno : '' ?>" 
                                        oninput="this.value=this.value.slice(0,10);"
                                        pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required />
                                </div>

                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-12 mb-4  align-items-center">

                                    <label for="zone" class="form-label">Zone<span style="color: red;">*</span></label>
                                    <select class="select form-control" id="zone" name="zone" required>
                                        <option value="" disabled selected>Choose zone</option>
                                        <?php foreach($zone as $val) { ?>
                                            <option value="<?= $val->id ?>">
                                                <?= $val->name ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                    

                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4  align-items-center">
                                <label for="type" class="form-label">User Type<span style="color: red;">*</span></label>

                                <select class="select form-control" id="type" name="type" required>
                                    <option value="" disabled selected>Choose User Type</option>
                                    <?php foreach($type as $val){?>
                                    <option value="<?=$val->id?>"><?=$val->name?></option>
                                    <?php }?>
                                </select>
                                </div>

                                <div class="col-md-6 mb-4  align-items-center">
                                <label for="type" class="form-label">Admin<span style="color: red;">*</span></label>

                                <select class="select form-control" id="admin" name="admin" required>
                                    <option value="" disabled selected>Choose Admin</option>
                                    <?php foreach($admin as $val){?>
                                    <option value="<?=$val->user_id?>"><?=$val->name?></option>
                                    <?php }?>
                                </select>
                                </div>
                            </div>

                            <div class ="row">

                                <div class="col-md-6 mb-4  align-items-center" id="clusterManager">
                                    <label for="cluster" class="form-label">Cluster Manager</label>

                                    <select class="select form-control" name="cluster" id="cluster">
                                        
                                    </select>
                                </div>

                                <div class="col-md-6 mb-4  align-items-center" id="bdpst">
                                    <label for="bdpstF" class="form-label">BDPST</label>
                                    <select class="select form-control" name="bdpstF" id="bdpstF">
                                        
                                    </select>
                                    
                                </div>
                            </div>

                        
                            <div class="row" id="showFields">
                                
                                <div class="col-md-6 mb-4  align-items-center">
                                    <label for="sales" class="form-label">Sales Coordinator</label>
                                    <select class="select form-control" name="sales" id="sales">
                                        
                                    </select>
                                    
                                
                                </div>

                                <div class="col-md-6 mb-4  align-items-center">
                                    <label for="pst" class="form-label">PST</label>
                                    <select class="select form-control" name="pst" id="pst">
                                        
                                    </select>
                                    
                                
                                </div>
                            </div>
                            <?php if (!isset($userDetails) && empty($userDetails)) { ?>
                                <div class="row">
                                    <!-- Password Field -->
                                    <div class="col-md-6 mb-4 align-items-center">
                                        <div data-mdb-input-init class="form-outline datepicker w-100">
                                            <label for="password" class="form-label">Password<span style="color: red;">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-lg" name="password" id="password" required/>
                                                <span class="input-group-text" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confirm Password Field -->
                                    <div class="col-md-6 mb-4 align-items-center">
                                        <div data-mdb-input-init class="form-outline datepicker w-100">
                                            <label for="confirmPassword" class="form-label">Confirm Password<span style="color: red;">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-lg" name="confirmPassword" id="confirmPassword" required/>
                                                <span class="input-group-text" onclick="togglePassword('confirmPassword')">
                                                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="mt-4 pt-2">
                                <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit" value="Submit" />
                            </div>

                        
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </section>
        </form>
</div>
<!-- /.container-fluid -->
</section>
</div></div>





<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


<script>
    $(document).ready(function() {
        // Event listener for form submission
        $('#regForm').on('submit', function(event) {
            var password = $('#password').val();
            var confirmPassword = $('#confirmPassword').val();

            if (password !== confirmPassword) {
                // Set the flash data in session using AJAX or display the message directly
                <?php $this->session->set_flashdata('error_message', 'Password Mismatch !!'); ?>

                // Optionally display the error message directly using jQuery
                alert('Password Mismatch !!');

                // Prevent the form from submitting
                event.preventDefault();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#zone').change(function() {
            var zoneId = $(this).val();
            if (zoneId) {
                // Fetch Cluster Managers
                $.ajax({
                    url: 'fetchClusterManagers',
                    type: 'POST',
                    dataType: 'json',
                    data: { zone_id: zoneId },
                    success: function(response) {
                        var clusterSelect = $('#cluster');
                        
                        // Clear existing options
                        clusterSelect.empty();

                        // Check if the response is an array and has data
                        if (Array.isArray(response) && response.length > 0) {
                            // Add default option
                            clusterSelect.append('<option value="" selected>Choose Cluster Manager</option>');
                            
                            // Add options from the response
                            $.each(response, function(index, item) {
                                clusterSelect.append('<option value="' + item.user_id + '">' + item.name + '</option>');
                            });
                        } else {
                            // Add "No data available" option
                            clusterSelect.append('<option value="" disabled selected>No data available</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: ", status, error);
                    }
                });

                // Fetch Sales Coordinators
                $.ajax({
                    url: 'fetchSalesCoordinators', 
                    type: 'POST',
                    dataType: 'json',
                    data: { zone_id: zoneId },
                    success: function(response) {
                        var salesSelect = $('#sales');
                        
                        // Clear existing options
                        salesSelect.empty();

                        // Check if the response is an array and has data
                        if (Array.isArray(response) && response.length > 0) {
                            // Add default option
                            salesSelect.append('<option value="" selected>Choose Sales Coordinator</option>');
                            
                            // Add options from the response
                            $.each(response, function(index, item) {
                                salesSelect.append('<option value="' + item.user_id + '">' + item.name + '</option>');
                            });
                        } else {
                            // Add "No data available" option
                            salesSelect.append('<option value="" disabled selected>No data available</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: ", status, error);
                    }
                });

                // Fetch BDPST
                $.ajax({

                    url: 'fetchBdpst', // Your URL to fetch BDPST
                    type: 'POST',
                    dataType: 'json',
                    data: { zone_id: zoneId },
                    success: function(response) {
                        var bdpstSelect = $('#bdpstF');
                        
                        // Clear existing options
                        bdpstSelect.empty();

                        // Check if the response is an array and has data
                        if (Array.isArray(response) && response.length > 0) {
                            // Add default option
                            bdpstSelect.append('<option value="" selected>Choose BDPST</option>');
                            
                            // Add options from the response
                            $.each(response, function(index, item) {
                                bdpstSelect.append('<option value="' + item.user_id + '">' + item.name + '</option>');
                            });
                        } else {
                            // Add "No data available" option
                            bdpstSelect.append('<option value="" disabled selected>No data available</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: ", status, error);
                    }
                });

                // Fetch PST
                $.ajax({
                    url: 'fetchPst',
                    type: 'POST',
                    dataType: 'json',
                    data: { zone_id: zoneId },
                    success: function(response) {
                        var pstSelect = $('#pst');
                        
                        // Clear existing options
                        pstSelect.empty();

                        // Check if the response is an array and has data
                        if (Array.isArray(response) && response.length > 0) {
                            // Add default option
                            pstSelect.append('<option value="" selected>Choose PST</option>');
                            
                            // Add options from the response
                            $.each(response, function(index, item) {
                                pstSelect.append('<option value="' + item.user_id + '">' + item.name + '</option>');
                            });
                        } else {
                            // Add "No data available" option
                            pstSelect.append('<option value="" disabled selected>No data available</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: ", status, error);
                    }
                });
            }
        });

    });
</script>

<script>
    $(document).ready(function() {
        // Call the function when the page is loaded and when the type field is changed
        $('#type').on('change', function() {
            toggleFields();
        });

        function toggleFields() {
            const userType = parseInt($('#type').val()); // Get the selected value of user type
            console.log(userType);
            const salesField = $('#sales');
            const pstField = $('#pst');
            const clusterField = $('#cluster');
            const bdpst = $('#bdpstF');

            // Enable all fields initially
            salesField.prop('disabled', false);
            pstField.prop('disabled', false);
            clusterField.prop('disabled', false);
            bdpst.prop('disabled', false);

            if ((userType === 2) || (userType === 1)) {
                salesField.prop('disabled', true);
                pstField.prop('disabled', true);
                clusterField.prop('disabled', true);
                bdpst.prop('disabled', true);
            } else if (userType === 15) {
                pstField.prop('disabled', true);
                clusterField.prop('disabled', true);
                bdpst.prop('disabled', true);
                salesField.prop('disabled', true);
            } else if (userType === 4) {
                clusterField.prop('disabled', true);
                bdpst.prop('disabled', true);
                //salesField.prop('disabled', true);
                pstField.prop('disabled', true);
            }else if (userType === 9) {
                clusterField.prop('disabled', true);
                bdpst.prop('disabled', true);
                salesField.prop('disabled', true);
                pstField.prop('disabled', true);
            }else if (userType === 13) {
                clusterField.prop('disabled', true);
                bdpst.prop('disabled', true);
                //salesField.prop('disabled', true);
                //pstField.prop('disabled', true);
            }else if (userType === 5) {
                clusterField.prop('disabled', true);
                //bdpst.prop('disabled', true);
                salesField.prop('disabled', true);
                pstField.prop('disabled', true);
            }
        }

        // Optionally trigger the function when the page is first loaded in case user type is pre-selected
        toggleFields();
    });
</script>
<script>
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
</script>

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

</body>
</html>
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

.profile-pic-container {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #ddd;
            margin-bottom: 20px;
        }

        .profile-pic-container img {
            width: 100%;
            /* height: 100%; */
            object-fit: cover;
        }

        .file-upload-btn {
            margin: 10px 0;
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
    <form id="regForm" method="POST" action="<?=base_url();?>Menu/UserRegistration">
            <section class="gradient-custom">
                <div class="container py-5">
                    <div class="row justify-content-center align-items-center ">
                    <div class="col-md-12">
                        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5">Edit User Details for : <span style="color: red;"><?=$userDetails[0]->name?></span></h3>

                            <div class="container">
                                

                                <div class="profile-pic-container">
                                    <img id="profilePic" src="<?=$photo?>" alt="Profile Picture">
                                </div>

                                <input type="file" id="fileInput" class="file-upload-btn" accept="image/*">
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-4">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="fname"> First Name</label>
                                    <input type="text" id="fname" name="fname" class="form-control form-control-lg"
                                    value="<?= isset($nameArr) ? $nameArr[0] : '' ?>" />
                                    <input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>" />
                                    
                                </div>

                                </div>

                                <div class="col-md-4 mb-4">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="lname"> Last Name</label>
                                    <input type="text" id="lname" name="lname" class="form-control form-control-lg" value="<?= isset($nameArr) ? $nameArr[1] : '' ?>" />
                                    <input type="hidden" id="user_id" name="user_id" value="<?= $userId ?>" />
                                    
                                </div>

                                </div>

                                <div class="col-md-4 mb-4">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="text" id="email" name="email" class="form-control form-control-lg" value="<?= isset($userDetails) ? $userDetails[0]->email : '' ?>"/>
                                    
                                </div>

                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6 mb-4 d-flex align-items-center">

                                <div data-mdb-input-init class="form-outline datepicker w-100">
                                    <label for="Username" class="form-label">Username</label>
                                    <input type="text" class="form-control form-control-lg" name="Username" id="Username" value="<?= isset($userDetails) ? $userDetails[0]->username : '' ?>"/>
                                    
                                </div>

                                </div>
                                <div class="col-md-6 mb-4 align-items-center">

                                <div data-mdb-input-init class="form-outline">
                                    <label class="form-label" for="phone">Phone number</label>
                                    <input type="number" class="form-control form-control-lg" name="phone" id="phone" value="<?= isset($userDetails) ? $userDetails[0]->phoneno : '' ?>"/>
                                    
                                </div>

                                </div>
                            </div>

                            <div class="row">
                                    <!-- Password Field -->
                                    <div class="col-md-6 mb-4 align-items-center">
                                        <div data-mdb-input-init class="form-outline datepicker w-100">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-lg" name="password" id="password" value="<?= isset($userDetails) ? $userDetails[0]->password : '' ?>"/>
                                                <span class="input-group-text" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confirm Password Field -->
                                    <div class="col-md-6 mb-4 align-items-center">
                                        <div data-mdb-input-init class="form-outline datepicker w-100">
                                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control form-control-lg" name="confirmPassword" id="confirmPassword" value="<?= isset($userDetails) ? $userDetails[0]->password : '' ?>"/>
                                                <span class="input-group-text" onclick="togglePassword('confirmPassword')">
                                                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>




                                    <?php 

              
                

                ?>






                                    <?php if($base_cluster_of_user == 0){ ?>
                                    <div class="col-md-12 mb-4 align-items-center">
                                        <div data-mdb-input-init class="form-outline datepicker w-100">


                                         <div class="form-group col-md-6">
                                                    <label for="inputAddress2">Set Base Travel Cluster <span style="color: red;">*</span></label>
                                                <?php $clusters = $this->Menu_model->getClusterByUserId($user['user_id']); ?>
                                                <select id="cluster_id" name="cluster_id" class="form-control">
                                                    <option selected value="">Select Cluster</option>
                                                    <?php  foreach($clusters as $cluster){
                                                    if($cluster->travel_type == 'base'){
                                                    ?>
                                                    <option value="<?=$cluster->id;?>"><?=$cluster->clustername;?> (Travel Type : <?= empty($cluster->travel_type) ? 'Not Marked' : $cluster->travel_type; ?>)</option>
                                                    <?php }
                                                } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else{ ?>
                                        <div class="col-md-12 mb-4 align-items-center">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h2 class="text-center">Base Cluster</h2> <hr>
                                                    <p class="card-text"> <?= $base_title_message; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                </div>

                                <div class="mt-4 pt-2">
                                <input data-mdb-ripple-init class="btn btn-primary btn-lg" type="submit" value="Submit" />
                            </div>
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





<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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

<script>
$(document).ready(function() {
    // Trigger when the file input changes (file selected)
    $('#fileInput').on('change', function() {
        var file = this.files[0];

        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Update image preview
                $('#profilePic').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);

            // Prepare the form data for the AJAX request
            var formData = new FormData();
            formData.append('file', file);

            // AJAX call to upload the file
            $.ajax({
                url: '<?= base_url(); ?>Menu/uploadProfilePic/<?=$userId?>',  // Update with your controller URL
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('File uploaded successfully!');
                },
                error: function(xhr, status, error) {
                    alert('Error uploading file: ' + error);
                }
            });
        } else {
            alert('Please select a file!');
        }
    });
});
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
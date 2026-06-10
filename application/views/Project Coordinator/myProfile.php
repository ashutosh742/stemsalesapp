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
body{
    background: -webkit-linear-gradient(left, #3931af, #00c6ff);
}
.emp-profile{
    padding: 3%;
    margin-top: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
}
.profile-img{
    text-align: center;
}
.profile-img img{
    width: 70%;
    height: 100%;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head h5{
    color: #333;
}
.profile-head h6{
    color: #0062cc;
}
.profile-edit-btn{
    border: none;
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
}
.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #0062cc;
}
.profile-work{
    padding: 14%;
    margin-top: -15%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work a{
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.profile-tab label{
    font-weight: 600;
}
.profile-tab p{
    font-weight: 600;
    color: #0062cc;
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
<?php 
// dd($data);
$managerName=$this->Menu_model->get_reportingManager($data[0]->aadmin);?>


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
 <!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Apply for leave</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="post" action="leaveApply">
          <!-- From Date input -->
           <input type="hidden" name="aadmin" id="aadmin" value="<?=$data[0]->aadmin?>">
           <input type="hidden" name="uid" id="uid" value="<?=$uid?>">

           <div class="form-group">
                <label for="leaveType">Select Leave Type</label><span style="color:red">*</span>
                <select class="form-control" name="leaveType" id="leaveType" required>
                    <option value="" disabled selected>Select leave type</option>
                    <?php foreach($lvTypes as $val){?>
                        <option value="<?= $val->id ?>" data-balance="<?= $val->balance ?>"><?= $val->leave_type ?></option>
                        <?php }?>
                </select>
            </div>
            <div class="form-group">
                <label for="fromDate">From Date</label><span style="color:red">*</span>
                <input type="date" class="form-control" name="fromDate" id="fromDate" placeholder="Select from date" required>
            </div>

          <!-- To Date input -->
            <div class="form-group">
                <label for="toDate">To Date</label><span style="color:red">*</span>
                <input type="date" class="form-control" name="toDate" id="toDate" placeholder="Select to date" required>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="halfdayleave" id="flexRadioDefault2" value="0" checked>
                <label class="form-check-label" for="flexRadioDefault2">
                    Full Day
                </label>
            </div>      
            
            <div class="form-check">
                <input class="form-check-input" type="radio" name="halfdayleave" id="flexRadioDefault1" value="1">
                <label class="form-check-label" for="flexRadioDefault1">
                    Half Day
                </label>
            </div>

            <div class="form-group" id="halfDayLeaveType" >
                <label for="leaveType">Select Leave Type</label><span style="color:red">*</span>
                <select class="form-control" name="halfdayleaveType" id="">
                    <option value="" disabled selected>Select leave type</option>
                    <option value="1">First Half Leave (10 AM - 2:30 PM)</option>
                    <option value="2">Second Half Leave (2:30 PM - 7 PM)</option>
                </select>
            </div>

            <br>
          <!-- Reason textarea input -->
          <div class="form-group">
            <label for="reason">Reason</label><span style="color:red">*</span>
            <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Enter the reason for leave" required></textarea>
          </div>
          <div class="modal-footer">
            <button type="submit" id="leavesubmit" class="btn btn-primary">Submit</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="viewApprovedLeaves" tabindex="-1" role="dialog" aria-labelledby="viewApprovedLeavesTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Approved Leaves</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <!-- <form method="post" action="cancelLeaves"> -->
          <!-- From Date input -->
            <input type="hidden" name="aadmin" id="aadmin" value="<?=$data[0]->aadmin?>">
            <input type="hidden" name="uid" id="uid" value="<?=$uid?>">
            
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>                   
                </thead>
                <tbody>
                <?php 
                    foreach($lvData as $tda){ 
                        // var_dump($tda->leave_type);
                        $leaveType = $this->Menu_model->getLeaveTypebyId($tda->leave_type);
                        ?>
                    <tr>
                        <td><?= $leaveType[0]->leave_type?></td>
                        <td><?= $tda->start_date?></td>
                        <td><?= $tda->end_date?></td>
                        <td><?= $tda->reason?></td>
                        <td><?= $tda->status?></td>
                        <td>
                            <?php
                                if ($tda->status == 'approved') { ?>
                                    <button class="btn btn-danger" onclick="cancelLeave(<?= $tda->id ?>)">Cancel Leave</button>
                        <?php    }?>
                        
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <!-- </form> -->
      </div>
      
    </div>
  </div>
</div>

<section class="content">
    <div class="container emp-profile">
        <form method="post">
            <div class="row">
                <div class="col-md-4">
                    <div class="profile-img">
                        <div class="">
                        <img id="profilePic" src="<?=base_url();?><?=$data[0]->photo?>" alt="Profile Picture">
                            <!-- <input type="file" name="file"/> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="profile">
                        <h5>
                            <?=$data[0]->name?>
                        </h5>
                        <h6>
                        <?=$dep_name?>
                        </h6>
                                <!-- <p class="proile-rating">RANKINGS : <span>8/10</span></p> -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">About</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Timeline</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link" id="leave-tab" data-toggle="tab" href="#leave" role="tab" aria-controls="leave" aria-selected="false">Leave management</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="day-tab" data-toggle="tab" href="#day" role="tab" aria-controls="day" aria-selected="false">Day Management</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="button" class="profile-edit-btn" name="btnAddMore" value="Edit Profile" 
                        onclick="window.location.href='<?= base_url(); ?>Menu/UserEditAction/<?=$uid?>'"/>
                </div>

            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="profile-work">
                        <!-- <p>WORK LINK</p>
                        <a href="">Website Link</a><br/>
                        <a href="">Bootsnipp Profile</a><br/>
                        <a href="">Bootply Profile</a>
                        <p>SKILLS</p>
                        <a href="">Web Designer</a><br/>
                        <a href="">Web Developer</a><br/>
                        <a href="">WordPress</a><br/>
                        <a href="">WooCommerce</a><br/>
                        <a href="">PHP, .Net</a><br/> -->
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="tab-content profile-tab" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>User Name</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->username?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>User Id</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->user_id?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Name</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->name?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Email</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->email?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Phone</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->phoneno?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Reporting Manager</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$managerName[0]->name?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Date Of Joining</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->usercreateDate?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Zone</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->zoneName?></p>
                                        </div>
                                    </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Experience</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p>-</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Date Of Joining</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->usercreateDate?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Zone</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$data[0]->zoneName?></p>
                                        </div>
                                    </div>
                                    
                        </div>
                        <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="profile-tab">
                        <?php if($lvTypes){?>
                            <?php foreach($lvTypes as $val){?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label><?=$val->leave_type?></label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$val->balance?></p>
                                        </div>
                                    </div>
                                    <?php }?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Apply for leave</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><button id="applyButton" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                                            Apply
                                            </button></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Leave Status</label>
                                        </div>
                                        <?php if($lvData){?>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#viewApprovedLeaves">View Leaves</button>
                                            <!-- <p>Leave form <?=$lvData[0]->start_date?> to <?=$lvData[0]->end_date?> is <?=$lvData[0]->status?>.</p>      -->
                                        </div>
                                        <?php }else{?>
                                            <div class="col-md-6">
                                            <p>No leaves planned</p>     
                                        </div>
                                            <?php }?>
                                    </div>
                                    <?php }else{?>
                                        <h4>No leaves available</h4>
                                        <?php }?>
                                    </div>
                                    
                        <div class="tab-pane fade" id="day" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Day Start</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$dayData[0]->ustart??'Day Yet to Start'?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Day End</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$dayData[0]->uclose??"Day yet to end"?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Tasks Planned</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$taskData[0]->allTasks?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Tasks Completed</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$taskData[0]->completed?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Tasks Pending</label>
                                        </div>
                                        <div class="col-md-6">
                                            <p><?=$taskData[0]->pending?></p>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>           
    </div>
</section>
</div></div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


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
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>
<script>
$(document).ready(function () {
    $('#leavesubmit').on('click', function () {
       var fromDate =$('#fromDate').val();
       var toDate =$('#toDate').val();
       var leaveType =$('#leaveType').val();
       var leaveBalance = $('#leaveType').find(':selected').data('balance');

       var startDate = new Date(fromDate);
        var endDate = new Date(toDate);
        var timeDifference = endDate - startDate;

        // Convert time difference from milliseconds to days (1 day = 1000 * 60 * 60 * 24 milliseconds)
        var dateDiff = timeDifference / (1000 * 60 * 60 * 24);
        var dateDiff = dateDiff + 1;
        if(fromDate > toDate){
            alert('Enter a valid date selection')
            return false;
        }
        if(dateDiff > leaveBalance){
            alert('You dont have enough leave balance for this selection')
            return false;
        }
        
        //return true;
    });
});
</script>

<script>
    // Get references to the radio buttons and the select dropdown
    const fullDayRadio = document.getElementById('flexRadioDefault2');
    const halfDayRadio = document.getElementById('flexRadioDefault1');
    const leaveTypeDropdown = document.getElementById('halfDayLeaveType');

    // Function to toggle the visibility of the leave type dropdown based on selected radio button
    function toggleLeaveTypeDropdown() {
        if (halfDayRadio.checked) {
            leaveTypeDropdown.setAttribute('required', 'true');
            leaveTypeDropdown.style.display = 'block'; // Show dropdown
        } else {
            leaveTypeDropdown.style.display = 'none';
            leaveTypeDropdown.style.display = 'none'; // Hide dropdown
        }
    }

    // Initially call the function to set the correct visibility based on the default selection
    toggleLeaveTypeDropdown();

    // Add event listeners to radio buttons to call the function on change
    fullDayRadio.addEventListener('change', toggleLeaveTypeDropdown);
    halfDayRadio.addEventListener('change', toggleLeaveTypeDropdown);


    function cancelLeave(leaveId) {

        if (confirm("Are you sure you want to cancel this leave?")) {
            // Use AJAX to send the leave ID to the server
            $.ajax({
                url: 'cancelLeave', // URL to your cancelLeave function
                method: 'POST',
                data: { id: leaveId },
                success: function(response) {
                    var result = JSON.parse(response); // Parse the JSON response
                    if (result.status == 'success') {
                        alert(result.message);
                        location.reload(); // Optionally reload the page to see changes
                    } else {
                        alert(result.message);
                    }
                },
                error: function() {
                    alert("An error occurred while canceling the leave.");
                    location.reload();
                }
            });
        }
    }



</script>

</body>
</html>
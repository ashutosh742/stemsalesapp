<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      
      <li class="nav-item d-none d-sm-inline-block">
        <a href="Dashboard" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block"> 
        <button type="button" class="btn btn-primary" onclick="goBack()">Go Back</button>
        <button type="button" class="btn btn-secondary" onclick="goForward()">Go Forward</button>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      
      <!-- Messages Dropdown Menu
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            
            <div class="media">
              <img src="<?=base_url();?>assets/image/user/Team.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  User 1
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            
            <div class="media">
              <img src="<?=base_url();?>assets/image/user/Team.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                User 2
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            
            <div class="media">
              <img src="<?=base_url();?>assets/image/user/Team.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                User 3
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>-->
      <!-- Notifications Dropdown Menu -->
      <h4>HI!  <?=$user['name']?></h4>
      <input type="hidden" id="ur_id" value="<?=$uid?>">
      <li class="nav-item">
        <a class="nav-link" href="<?=base_url();?>/Menu/Notification">
          <i class="far fa-bell"></i>
          <?php
          $udetail = $this->Menu_model->get_userbyid($uid);
          $admid = $udetail[0]->admin_id;
          $notify=$this->Menu_model->notify($uid);?>
          <span class="badge badge-warning navbar-badge"><?=sizeof($notify);?></span>
        </a> 
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
<img id="mainlogo" src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" width="80%" class="p-3">
    <center><h5 class="text-white"><b>STEM APP</b></h5></center>
    <hr>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?=base_url();?>assets/image/user/Team.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">User Name</a>
        </div>
      </div>
      
      
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/Dashboard" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/CategoryStatusPage" >
              <i class="far fa-circle nav-icon"></i>
              <p>Category Wise Status</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/TeamDailyReport/<?=date('Y-m-d')?>" >
              <i class="far fa-circle nav-icon"></i>
              <p>Team Daily Report</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/AllReviewPlaing" >
              <i class="far fa-circle nav-icon"></i>
              <p>Plan BD Review</p>
            </a>
          </li>
          
           <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/Mytarget" >
              <i class="far fa-circle nav-icon"></i>
              <p>My target</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/AllPSTReviewPlaing" >
              <i class="far fa-circle nav-icon"></i>
              <p>Plan PST Review</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/ReviewReport" >
              <i class="far fa-circle nav-icon"></i>
              <p>All Review Report</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/PSTReviewReportSummary" >
              <i class="far fa-circle nav-icon"></i>
              <p>PST Review Summary</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/BDReviewReportSummary" >
              <i class="far fa-circle nav-icon"></i>
              <p>BD Review Summary</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/NOREVIEWCOMP" >
              <i class="far fa-circle nav-icon"></i>
              <p>No Review Company</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/TaskCheck" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Task Check</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/BDWORKBWDBYTC" >
              <i class="far fa-circle nav-icon"></i>
              <p>Work B/W Date</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/RPTHISYEAR" >
              <i class="far fa-circle nav-icon"></i>
              <p>RP This Year</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/BDNotWorkDD" >
              <i class="far fa-circle nav-icon"></i>
              <p>NO Work B/W Date</p>
            </a>
          </li>
          <!--<li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/CONVERSIONBWDATE" >
              <i class="far fa-circle nav-icon"></i>
              <p>Conversion B/W Date</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/PSTWorkOnRPCompanies" >
              <i class="far fa-circle nav-icon"></i>
              <p>PST Work On RP Companies (Newly Assigned) </p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/PSTWorkOnRPALLCompanies" >
              <i class="far fa-circle nav-icon"></i>
              <p>PST Work On RP Companies (All Assigned)</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/PSTWorkOnRPPACompanies" >
              <i class="far fa-circle nav-icon"></i>
              <p>PST Work On RP Companies (Previously Assigned)</p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/ExpectedSchool" >
              <i class="far fa-circle nav-icon"></i>
              <p>Expected School</p>
            </a>
          </li>-->
          
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/AllUtilisation" >
              <i class="far fa-circle nav-icon"></i>
              <p>All Utilisation</p>
            </a>
          </li>
          
          
          
          
          <!--
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/AnnualReviewDetail" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Annual Review</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/DayManagement" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Day Management</p>
            </a>
          </li>
          -->
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/logout" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Logout</p>
            </a>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
      <hr><center><lable class="text-warning"><b>Alert!</b></lable></center><hr>
      <span id="alsmss"></span>
    </div>
    <!-- /.sidebar -->
  </aside>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script> 
<script> 



$(document).ready(function() {
    trackLocation();
});

function handleGeolocationError() {
   const bodyElement = document.querySelector("body");
   bodyElement.style.display = "none";
   alert('Error: Geolocation is not available or location services are turned off.');
}
function handleGeolocationSuccess(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    const contentDiv = document.getElementById("content");
    contentDiv.style.display = "block";
}
function getLocation() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(handleGeolocationSuccess, handleGeolocationError);
    } else {
        const errorMessage = document.getElementById("error-message");
        errorMessage.style.display = "block";
    }
}
window.onload = getLocation;



function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
        })
        .catch(function(error) {
             const bodyElement = document.querySelector("body");
             bodyElement.style.display = "none";
             alert('Error: Camera access permission denied.');
        });
}
startCamera();




function trackLocation() {
    if ("geolocation" in navigator) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          var ur_id = document.getElementById("ur_id").value;
          var latitude = position.coords.latitude;
          var longitude = position.coords.longitude;
            $.ajax({
                url:'<?=base_url();?>Menu/store_location',
                 method: 'post',
                 data: {latitude: latitude, longitude: longitude, ur_id: ur_id},
                 success: function(result){
                }
            });
        },
        function (error) {
          console.error("Error getting location: " + error.message);
        }
      );
    } 
    else {console.error("Geolocation is not supported by this browser.");} 
}


function goBack() { window.history.back(); }
function goForward() { window.history.forward(); }
  
    var ur_id = document.getElementById("ur_id").value;
    $.ajax({
    url:'<?=base_url();?>Menu/adminpopup',
     method: 'post',
     data: {ur_id: ur_id},
     success: function(result){
        var res = result;
        $("#alsmss").html(result);
    }
    });
    
</script>  
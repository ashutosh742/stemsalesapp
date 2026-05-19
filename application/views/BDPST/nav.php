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
      <h4>HI!  <?=$user['name']?> <b>Cash Balance (Rs. 1000/-)</b></h4>
      <input type="hidden" id="ur_id" value="<?=$uid?>">
      <li class="nav-item">
        <a class="nav-link" href="<?=base_url();?>/Menu/Notification">
          <i class="far fa-bell"></i>
          <?php $notify=$this->Menu_model->notify($uid);?>
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
      <?php $userName=$this->Menu_model->get_userName($uid);?>

        <a href="<?=base_url();?>Menu/myProfile" class="d-block"><?=$userName[0]->name?></a>

      </div></div>
      
      
      
      
      <a href="https://meet.google.com/vbk-btki-wcw" class="btn btn-primary" target="_blank">Handover Timeline Link</a>
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
            <a href="<?=base_url();?>Menu/HandBIND" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Handover to Installation</p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/NewLead" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Add New Lead</p>
            </a>
          </li>
          
          
          <!--<li class="nav-item">
            <a href="<?=base_url();?>Menu/HandBINDDetail/6" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Installation Detail</p>
            </a>
          </li>-->
          
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/DayManagement" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Day Management</p>
            </a>
          </li>
          <li class="nav-item">
                    <a href="<?=base_url();?>Menu/YesterDayDaysCloseRequest" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Day Close Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?=base_url();?>Menu/GetTodaysTeamDayChnageRequestData" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Day Change Request</p>
                    </a>
                </li>
 
                <li class="nav-item">
                <a href="<?=base_url();?>Menu/PlannerTaskApprovelPage" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Planner Task Approvel</p>
                </a>
          </li>
            
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/TodaysTaskApprovelRequest" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Todays Task Approvel Request</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/SpecialRequestForLeaveSomeTimeData" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Team Special Request For Leave Some Time </p>
            </a>
        </li>
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/Mytarget" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>My Target</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/assignpst" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>RP Check</p>
            </a>
          </li>
          
          <!--<li class="nav-item">-->
          <!--  <a href="<?=base_url();?>Menu/AddFAQ" class="nav-link">-->
          <!--    <i class="far fa-circle nav-icon"></i>-->
          <!--    <p>Add FAQ</p>-->
          <!--  </a>-->
          <!--</li>-->
          <!--<li class="nav-item">-->
          <!--  <a href="<?=base_url();?>Menu/FAQReport" class="nav-link">-->
          <!--    <i class="far fa-circle nav-icon"></i>-->
          <!--    <p>FAQ Report</p>-->
          <!--  </a>-->
          <!--</li>-->
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/HumHongeTaiyar" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Hum Honge Taiyar</p>
            </a>
           </li>
           <li class="nav-item">
            <a href="<?=base_url();?>Menu/HumHongeTaiyarReport" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Hum Honge Taiyar Report</p>
            </a>
           </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/AllReviewPlaing" >
              <i class="far fa-circle nav-icon"></i>
              <p>All Review</p>
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/cluster" >
              <i class="far fa-circle nav-icon"></i>
              <p>Add travel Cluster</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewDet" >
              <i class="far fa-circle nav-icon"></i>
              <p>Annual Review</p>
            </a>
          </li>
         
          <!--<li class="nav-item">-->
          <!--  <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewReport" >-->
          <!--    <i class="far fa-circle nav-icon"></i>-->
          <!--    <p>Annual Review Report</p>-->
          <!--  </a>-->
          <!--</li>-->
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewReportDatabkp" >
              <i class="far fa-circle nav-icon"></i>
              <p>Annual Review Report</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/ReviewReport" >
              <i class="far fa-circle nav-icon"></i>
              <p>Review Report</p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/CategoryStatusPage" >
              <i class="far fa-circle nav-icon"></i>
              <p>Category Wise Status</p>
            </a>
          </li>
          
          
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/logout" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Logout</p>
            </a>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
      <!-- /<hr><center><lable class="text-warning"><b>Alert!</b></lable></center><hr>
      <span id="alsmss"></span>
    </div>
    <!-- /.sidebar -->
  <hr><center><lable class="text-warning"><b>Alert!</b></lable></center><hr>
      <span id="alsmss"></span>
      <hr><center><lable class="text-warning"><b>Opration Update!</b></lable></center><hr>
      <span id="opalsms"></span>
      <hr><center><lable class="text-warning"><b>Notification!</b></lable></center><hr>
      <span id="nitisms"></span>
    </div>
    <!-- /.sidebar -->
  </aside>
  <?php 
      if(!isset($daycheck)){
        $current_uid    = $user['user_id'];
      $user_type = $user['type_id'];
      $user_day = $this->Menu_model->get_daydetail($current_uid,date("Y-m-d"));
      if(sizeof($user_day) == 0){
          $this->session->set_flashdata('error_message','* Please start your day first');
          redirect('Menu/DayManagement');
      }  
      }    
      ?>
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
    url:'<?=base_url();?>Menu/bdpopup',
     method: 'post',
     data: {ur_id: ur_id},
     success: function(result){
        var res = result;
        $("#alsmss").html(result);
    }
    });
    
    
    $.ajax({
    url:'<?=base_url();?>Menu/opalsms',
     method: 'post',
     data: {ur_id: ur_id},
     success: function(result){
        var res = result;
        $("#opalsms").html(result);
    }
    });
    
    
    $.ajax({
    url:'<?=base_url();?>Menu/nitisms',
     method: 'post',
     data: {ur_id: ur_id},
     success: function(result){
        var res = result;
        $("#nitisms").html(result);
    }
    });
    
    
    
</script>  
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul id="notifications"></ul>
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
  <center>
    <h5 class="text-white"><b>STEM APP</b></h5>
  </center>
  <hr>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?=base_url();?>assets/image/user/Team.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <?php 
          $userName=$this->Menu_model->get_userName($uid);?>
        <a href="<?=base_url();?>Menu/myProfile" class="d-block"><?=$userName[0]->name?></a>
      </div>
    </div>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
          with font-awesome or any other icon font library -->
      <li class="nav-item">
          <a href="<?= base_url(); ?>Menu/Dashboard" class="nav-link">
               <i class="fas fa-home nav-icon"></i>
              <p>Dashboard</p>
          </a>
      </li>
      <li class="nav-item">
          <a href="<?= base_url(); ?>Menu/DayManagement" class="nav-link">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>Day Management</p>
          </a>
      </li>
 <li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-address-card"></i>
        <p>
            Contact Management
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="<?= base_url(); ?>Menu/GetAllCompaniesThatDoNotHavePrimaryContactDetailsData" class="nav-link">
                <i class="nav-icon fas fa-address-book"></i>
                <p>No Primary Contact Companies</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url(); ?>Menu/GetAllContactDetailsChangeRequest">
                <i class="nav-icon fas fa-user-edit"></i>
                <p>Contact Add or Edit Request</p>
            </a>
        </li>
    </ul>
</li>
    
      
       
      
       
     
      <li class="nav-item">
          <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>
                  User Management
                  <i class="right fas fa-angle-left"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <!-- <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/UserRegistration">
                      <i class="nav-icon fas fa-user-plus"></i>
                      <p>User Entry Form</p>
                  </a>
              </li> -->
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/UserRegistrationNew">
                      <i class="nav-icon fas fa-user-plus"></i>
                      <p>User Entry Form</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/UserDisplayPage">
                      <i class="nav-icon fas fa-user-edit"></i>
                      <p>User Details</p>
                  </a>
              </li>
          </ul>
      </li>
      <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-user-clock"></i>
                <p>
                    User Day Management
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url(); ?>Menu/GetTodaysTeamDayChnageRequestData">
                        <i class="nav-icon fas fa-calendar-plus"></i>
                        <p>Day Change Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= base_url(); ?>Menu/YesterDayDaysCloseRequest" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Day Close Request</p>
                    </a>
                </li>
            </ul>
        </li>
        <!--<li class="nav-item">
          <a href="<?=base_url();?>Menu/LiveTaskTracking" class="nav-link">
          
            <i class="far fa-circle nav-icon"></i>
          
            <p>Live Task Tracking</p>
          
          </a>
          
          </li>-->
        <!-- <li class="nav-item">
          <a href="<?=base_url();?>Menu/TodaysTaskApprovelRequest" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Planner Approval Request</p>
          </a>
          </li>
          <li class="nav-item">
          <a href="<?=base_url();?>Menu/PlannerTaskApprovelPage" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Task Approval Request</p>
          </a>
          </li> -->
       <li class="nav-item">
          <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>
                  Planner Management
                  <i class="right fas fa-angle-left"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/TodaysTaskApprovelRequest">
                      <i class="nav-icon fas fa-check-circle"></i>
                      <p>Request Approval</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/CheckAllCreatePlannerRequest">
                      <i class="nav-icon fas fa-clock"></i>
                      <p>Pending Task Planner Request</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/PlannerTaskApprovelPageTestData">
                      <i class="nav-icon fas fa-check-square"></i>
                      <p>Task Approval</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/AssignTask">
                      <i class="nav-icon fas fa-user-plus"></i>
                      <p>Assign Task</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Management/SpecialRestrictionOnTaskPlanner">
                      <i class="nav-icon fas fa-ban"></i>
                      <p>Special Restriction on Task Planner</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/SpecialRequestForLeaveSomeTimeData">
                      <i class="nav-icon fas fa-sign-out-alt"></i>
                      <p>Team Special Request For Leave Some Time</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/MandatoryRestrictionforPlannerPage" class="nav-link">
                      <i class="nav-icon fas fa-exclamation-triangle"></i>
                      <p>Mandatory Restriction for Planner Page</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/GetAllCompulsiveAndNeedYourAttentionLog" class="nav-link">
                      <i class="nav-icon fas fa-exclamation-circle"></i>
                      <p>Compulsive & Need Your Attention Log</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/GetAllPlannerLogPlannedByUsers" class="nav-link">
                      <i class="nav-icon fas fa-list-alt"></i>
                      <p>All Planner Log Planned By Users</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/PendingForWriteMomMeetingList" class="nav-link">
                      <i class="nav-icon fas fa-file-alt"></i>
                      <p>Pending For Write Mom Meeting List</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/GetAllUserCurrentDayActivityOnAPP" class="nav-link">
                      <i class="nav-icon fas fa-mobile-alt"></i>
                      <p>User Current Day Activity On APP</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/GetUserMarkedCurrentYearFocusFunnels" class="nav-link">
                      <i class="nav-icon fas fa-bullseye"></i>
                      <p>User Marked Current Year Focus Funnels</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/EarlyPlannerRequestData" class="nav-link">
                      <i class="nav-icon fas fa-calendar-plus"></i>
                      <p>Team Early Planner Request</p>
                  </a>
              </li>
          </ul>
      </li>
              <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>
                            Pending Meetings Task
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url(); ?>Menu/TodaysPendingMeetingsTask">
                                <i class="nav-icon fas fa-calendar-day"></i>
                                <p>Todays Pending Meetings Task</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url(); ?>Menu/GetOurOrTeamTodaysPendingMeetingsTaskRequest">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Todays Pending Meetings Task Request</p>
                            </a>
                        </li>
                    </ul>
                </li>

            


     <!-- <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Management/CheckingDayManagement_New">
            <i class="fas fa-chart-line nav-icon"></i>
            Day Check Management
          </a>
        </li>
       
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Management/ApproveDayCheckRequest">
            <i class="fas fa-chart-line nav-icon"></i>
            Approve Day Check Request
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Menu/ApproveTaskCheckRequest">
          <i class="fas fa-chart-line nav-icon"></i>
          Approve Task Check Request
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Management/DayManagementReport">
            <i class="fas fa-chart-line nav-icon"></i>
            Day Check Report
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Menu/TaskCheck_New">
            <i class="fas fa-chart-bar nav-icon"></i>
            Task Check Management
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Menu/TaskCheck_NewReport">
            <i class="fas fa-chart-bar nav-icon"></i>
            Task Check Report
          </a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/NewFunnel" >
            <i class="far fa-circle nav-icon"></i>
            <p>New Funnel Added</p>
          </a>
        </li> -->
       <!-- <li class="nav-item">
            <a class="nav-link" href="<?= base_url(); ?>Menu/CheckCompanyCreateBetweenDate">
                <i class="nav-icon fas fa-calendar-range"></i>
                <p>Company Created Between Dates</p>
            </a>
        </li> -->
       
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/CategoryStatusPage" >
            <i class="far fa-circle nav-icon"></i>
            <p>Category Wise Status</p>
          </a>
        </li> -->
         <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/NotworkCompanyBDPST" >
            <i class="far fa-circle nav-icon"></i>
            <p>Not Work Company by BDPST</p>
          </a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/TeamDailyReport/<?=date('Y-m-d')?>" >
            <i class="far fa-circle nav-icon"></i>
            <p>Team Daily Report</p>
          </a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/AllReviewPlaing" >
            <i class="far fa-circle nav-icon"></i>
            <p>Our Planed Review</p>
          </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>SalesReviews/index">
                <i class="fas fa-calendar-check nav-icon"></i>
                <p>BD Wise Sales Review</p>
            </a>
        </li>


        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/targetVsAchievedData" >
            <i class="far fa-circle nav-icon"></i>
            <p>Target vs Achived Data Report</p>
          </a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/AllPSTReviewPlaing" >
            <i class="far fa-circle nav-icon"></i>
            <p>Plan PST Review</p>
          </a>
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/ReviewDataReport" >
            <i class="far fa-circle nav-icon"></i>
            <p>New Review Report</p>
          </a>
        </li> -->
   
        <!-- <li class="nav-item">
          <a href="#" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                  New Annual Review Report
                  <i class="right fas fa-angle-left"></i>
              </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                  <a class="nav-link" href="<?= base_url(); ?>Menu/NewAnnualReviewReport">
                      <i class="nav-icon fas fa-file-contract"></i>
                      <p>Annual Review Report</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/NewAnnualReviewReportWithLastYearMeetingsDetails" class="nav-link">
                      <i class="nav-icon fas fa-file-invoice"></i>
                      <p>Annual Review Report With Last Year Meeting Details</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= base_url(); ?>Menu/GetUserMarkedCurrentYearFocusFunnels" class="nav-link">
                      <i class="nav-icon fas fa-bullseye"></i>
                      <p>User Marked Current Year Focus Funnels</p>
                  </a>
              </li>
          </ul>
      </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/ReviewReport" >
            <i class="far fa-circle nav-icon"></i>
            <p>All Review Report</p>
          </a>
        </li> -->
        <!-- <li class="nav-item">
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
        </li> -->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/BDNotWorkDD" >
            <i class="far fa-circle nav-icon"></i>
            <p>NO Work B/W Date</p>
          </a>
        </li> -->
        <!-- <li class="nav-item">
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
        </li> -->
        <!--<li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/CONVERSIONBWDATE" >
          
            <i class="far fa-circle nav-icon"></i>
          
            <p>Conversion B/W Date</p>
          
          </a>
          
          </li>
          
          
          
          <li class="nav-item">
          
          <a class="nav-link" href="<?=base_url();?>Menu/ExpectedSchool" >
          
            <i class="far fa-circle nav-icon"></i>
          
            <p>Expected School</p>
          
          </a>
          
          </li>-->
       <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Menu/AllUtilisation">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>All Utilisation</p>
          </a>
      </li>
        <?php /*
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-circle"></i>
            <p>
              Analysis
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>
                  Task Graph
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/StatusWiseTaskAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Status Wise Task Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/ActionWiseFunnelAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Action Wise Funnel Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/OtherUserOnFunnelAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Other User On Funnel Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/TimeSlotWiseTaskAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Time Slot Wise Task Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/ActionWiseTaskConversion" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Action Wise Task Conversion</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/StatusWiseTaskConversion" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Status Wise Task Conversion</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/TaskWiseDetailAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Task Wise Detail Analysis</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>
                  Funnel Graph
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/FunnelAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Funnel Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/CityWiseFunnelAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>City Wise Funnel Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/PartnerWiseFunnelAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Partner Wise Funnel Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/CategoryWiseFunnelAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Category Wise Funnel Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/CompanyWithSameStatusSinceFunnleAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Company With Same Status Analysis</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/StageWiseFunnleAnalysis" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>Stage Wise Funnle Analysis</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>
                  R Graph
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/BDRequest" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>BD Request</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?= base_url(); ?>GraphNew/PendingRIDDayWise" class="nav-link">
                    <i class="far fa-dot-circle nav-icon"></i>
                    <p>RID Day Wise</p>
                  </a>
                </li>
                <!-- <li class="nav-item">
                  <a href="#" class="nav-link">
                  <i class="far fa-dot-circle nav-icon"></i>
                  <p>Level 3</p>
                  </a>
                  </li> -->
              </ul>
            </li>
          </ul>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Management/HolidayList" >
            <i class="far fa-circle nav-icon"></i>
            <p>Manage Holiday List</p>
          </a>
        </li>
         <!-- <li class="nav-item dropdown">
          <a href="" class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          
          <i class="far fa-circle nav-icon"></i>
          
          <p>Analysis</p>
          
          </a>
          
          <div class="dropdown-menu bg-dark" aria-labelledby="navbarDropdown">
           <a class="dropdown-item" href="<?= base_url(); ?>Graphs/Fgraphs">FGraph</a>
           
            <a class="dropdown-item" href="<?= base_url(); ?>Menu/day">DGraph</a>
          
            <a class="dropdown-item" href="<?= base_url(); ?>Menu/request">RGraph</a>
          
            <a class="dropdown-item" href="<?= base_url(); ?>Menu/region">ROGraph</a>
          
            <a class="dropdown-item" href="<?= base_url(); ?>Menu/task">TGraph</a>
          
          </div>
          
          </li> -->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewReport" >-->
        <!--    <i class="far fa-circle nav-icon"></i>-->
        <!--    <p>Annual Review Report</p>-->
        <!--  </a>-->
        <!--</li>-->
        <!-- <li class="nav-item">
          <a class="nav-link" href="<?=base_url();?>Menu/ReviewPage" >
            <i class="far fa-circle nav-icon"></i>
            <p>Annual Review Report</p>
          </a>
        </li> -->
        <li class="nav-item">
            <a href="<?=base_url();?>Menu/TeamCluster" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Team Travel Cluster</p>
            </a>
          </li>
         <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Team Travel Adavance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurTeamTravelAdvanceRequest" class="nav-link">
                            <i class="fa-solid fa-hand-point-right"></i>
                                <p>Our Team Travel Advance Request</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/CashExpenseReport" class="nav-link">
                            <i class="fa-solid fa-hand-point-right"></i>
                                <p>Team Cash Expense Request</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurTeamTravelAdvanceReport" class="nav-link">
                            <i class="fa-solid fa-hand-point-right"></i>
                                <p>Team Travel Advance Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurTeamCashExpenseReport" class="nav-link">
                            <i class="fa-solid fa-hand-point-right"></i>
                                <p>Team Cash Expense Report</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewReportDataInAdmin" >-->
        <!--    <i class="far fa-circle nav-icon"></i>-->
        <!--    <p>Annual Review Report</p>-->
        <!--  </a>-->
        <!--</li>-->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="<?=base_url();?>Menu/HumHongeTaiyarReportAdmin" >-->
        <!--    <i class="far fa-circle nav-icon"></i>-->
        <!--    <p>Hum Honge Taiyar Report</p>-->
        <!--  </a>-->
        <!--</li>-->
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="<?=base_url();?>Menu/HumHongeTaiyarReportApprove" >-->
        <!--    <i class="far fa-circle nav-icon"></i>-->
        <!--    <p>Hum Honge Taiyar Approve Report</p>-->
        <!--  </a>-->
        <!--</li>-->
       
       
        <!--<li class="nav-item">-->
        <!--  <a class="nav-link" href="<?=base_url();?>Menu/FAQReport" >-->
        <!--    <i class="far fa-circle nav-icon"></i>-->
        <!--    <p>FAQ Report</p>-->
        <!--  </a>-->
        <!--</li>-->
                <li class="nav-item">
            <a href="<?=base_url();?>Menu/TodaysUserTotalTimeSpentByUrlPath" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Time Spent By User</p>
            </a>
          </li>
          <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
            Add Target To User
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url(); ?>Menu/AddTarget">
                  <i class="fa-solid fa-hand-point-right"></i>
                  Add Target
                </a>
              </li>
             
              <li class="nav-item">
                <a href="<?=base_url();?>Menu/PendingTargetDetailsAfterReviewByUser" class="nav-link">
                  <i class="fa-solid fa-hand-point-right"></i>
                  <p>Target Pending After Review Close</p>
                </a>
              </li>
        
              <li class="nav-item">
                <a href="<?=base_url();?>Menu/AddTargetListByUser" class="nav-link">
                  <i class="fa-solid fa-hand-point-right"></i>
                  <p> Target List</p>
                </a>
              </li>
          </ul>
        </li>
         <!-- 
          <li class="nav-item">
          
          <a href="<?=base_url();?>Menu/DayManagement" class="nav-link">
          
          <i class="far fa-circle nav-icon"></i>
          
          <p>Day Management</p>
          
          </a>
          
          </li> -->
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
	
  	<li class="nav-item">
            <a href="<?=base_url();?>Menu/AddTarget" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Add Target</p>
            </a>
          </li>
          
          -->
        */ ?>
        
       <li class="nav-item">
          <a class="nav-link" href="<?= base_url(); ?>Menu/GetAllTaskExecutionPermissionsbySC">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Task Execution Request By SC</p>
          </a>
      </li>
      <li class="nav-item">
          <a href="<?= base_url(); ?>Menu/DynamicTaskFormForSC" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>Task Manage For SC</p>
          </a>
      </li>
         
       
          
        <li class="nav-item">
          <a href="<?=base_url();?>Menu/logout" class="nav-link">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            <p>Logout</p>
          </a>
        </li>
    
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
              <?php 
      $getcurUrl = $this->Menu_model->getCurrentUrl();
      
      $notAssignAfterRejectCount  = $this->Menu_model->GetTeamRejectedTaskButNotAssignCount($uid,date("Y-m-d"));
      if($notAssignAfterRejectCount > 0){
            $checkonUrl = base_url().'Menu/PlannerTaskApprovelPageTestData';
            if($getcurUrl !== $checkonUrl){
              $needle = 'Menu/CheckTaskDetailsByUser';
                $urlexistsorNot = $this->Menu_model->hasCheckTaskDetails($getcurUrl,$needle);
                if (!$urlexistsorNot) {
                     if (strpos($getcurUrl, 'AssignTaskById') !== false) {
                 }else{
                    $this->Menu_model->GetTeamRejectedTaskButNotAssign($uid,date("Y-m-d")); 
                 }
                }
            }
        }
      ?>
    <hr>
    <center>
      <lable class="text-warning"><b>Alert!</b></lable>
    </center>
    <hr>
    <span id="alsmss"></span>
  </div>
  <!-- /.sidebar -->
</aside>
<style>
  .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
    background-color: #790a40;
    color: #fff;
}
  [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active, [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus, [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
    background-color: rgba(255, 255, 255, .9);
    color: #343a40;
    padding: 4px;
    margin-top: 4px;
}
</style>
<script>
  $(document).ready(function () {
    var currentUrl = window.location.href;
    $('.nav-link').each(function () {
        if (this.href === currentUrl) {
            // Add active class to the current link
            $(this).addClass('active');
            // If this is inside a submenu, open parent
            $(this).closest('.nav-treeview').parent().addClass('menu-open');
            $(this).closest('.nav-treeview').prev('.nav-link').addClass('active');
        }
    });
});
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script> 

<script async src="https://www.googletagmanager.com/gtag/js?id=G-PEGQELMXKQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PEGQELMXKQ');
</script>
<!-- jQuery UI -->
<script> 
  // $(document).ready(function() {
  //     // trackLocation();
  // });
  
  
  
  // function handleGeolocationError() {
  
  //    const bodyElement = document.querySelector("body");
  
  //    bodyElement.style.display = "none";
  
  //    alert('Error: Geolocation is not available or location services are turned off.');
  
  // }
  
  // function handleGeolocationSuccess(position) {
  
  //     const latitude = position.coords.latitude;
  
  //     const longitude = position.coords.longitude;
  
  //     const contentDiv = document.getElementById("content");
  
  //     contentDiv.style.display = "block";
  
  // }
  
  // function getLocation() {
  
  //     if ("geolocation" in navigator) {
  
  //         navigator.geolocation.getCurrentPosition(handleGeolocationSuccess, handleGeolocationError);
  
  //     } else {
  
  //         const errorMessage = document.getElementById("error-message");
  
  //         errorMessage.style.display = "block";
  
  //     }
  
  // }
  
  // window.onload = getLocation;
  
  
  
  
  
  
  
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
  
  // startCamera();
  
  
  
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
<script>
  $(document).ready(function() {  
    //alert("inside");
    var admin_id = <?=$uid?>;
    console.log("Admin ID:", admin_id);
    
    // checkNotifications(admin_id);
    
   // setInterval(function() {
   //     checkNotifications(admin_id); 
  //  }, 10000); 
  });
  
  function checkNotifications(admin_id) {
    console.log("Checking notifications for admin_id: " + admin_id);
    $.ajax({
        url: '<?=base_url();?>Menu/check_notifications', 
        method: 'post',
        dataType: 'json', 
        data: { admin_id: admin_id },
        success: function(response) {
  
            if (response.length > 0) {
                response.forEach(function(notification) {
                  //console.log(notification);
                    alert('New Notification: ' + notification.sms);
                    markNotificationAsRead(notification.id); 
                });
            } else {
                console.log('No new notifications.');
            }
        },
        error: function(error) {
            console.error('Error fetching notifications:', error);
        }
    });
  }
  
  // Function to mark notifications as read
  function markNotificationAsRead(notificationId) {
    console.log("Marking notification as read, ID: " + notificationId);
    $.ajax({
        url: '<?=base_url();?>Menu/mark_notification_as_read', // Correct endpoint for marking as read
        method: 'post',
        data: { id: notificationId },
        dataType: 'json', // Expecting JSON data
        success: function() {
            console.log('Notification marked as read.');
        },
        error: function(error) {
            console.error('Error marking notification as read:', error);
        }
    });
  }
</script>
<style>
.bg-light,.bg-light>a,.card.card-outline-tabs{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2)}.card,.card.card-outline-tabs,.small-box>.inner,table{transition:box-shadow .2s ease-in-out;padding:10px;background:#fff8dc}.card.card-outline-tabs{border-top:0}.bg-light,.bg-light>a{border-radius:0;transition:box-shadow .2s ease-in-out}.card{box-shadow:none}.card,.main-footer,.navbar-white,.small-box>.inner,table{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2)}.navbar-white{background:#fff8dc}[class*=sidebar-dark-]{background:#3b1818;background:conic-gradient(from 180deg at 50% 50%,#220c0c,#220c0c,#0f0c22,#0c2215,#140c22,#220c13);animation:20s linear infinite spinGradient;transition:.2s ease-in-out}.nav-sidebar>.nav-item{box-shadow:rgba(60,64,67,.3) 0 1px 2px 0,rgba(60,64,67,.15) 0 2px 6px 2px}.fa-circle:before,.nav-sidebar .nav-item>.nav-link{font-size:13px;margin:-2px}#alsmss,span#alsmss{font-size:13px}#alsmss h5{font-size:15px}aside #mainlogo{filter:drop-shadow(0 0 1rem blue);animation:5s linear infinite spinGradient_image;transition:.2s}@keyframes spinGradient_image{0%{filter:drop-shadow(0 0 1rem #ff0f7b)}25%{filter:drop-shadow(0 0 1rem #fff95b)}50%{filter:drop-shadow(0 0 1rem #e60b09)}75%{filter:drop-shadow(0 0 1rem #59d102)}100%{filter:drop-shadow(0 0 1rem #6bdfdb)}}@keyframes spinGradient{0%{background:conic-gradient(from 90deg at 50% 50%,#220c0c,#220c0c,#0f0c22,#0c2215,#140c22,#220c13)}25%{background:radial-gradient(circle,#220c0c,#0f0c22,#0c2215,#140c22,#220c13)}100%,50%{background:conic-gradient(from 180deg at 50% 50%,#220c0c,#0f0c22,#0c2215,#140c22,#220c13,#220c0c)}75%{background:conic-gradient(from 270deg at 50% 50%,#220c0c,#0f0c22,#0c2215,#140c22,#220c13,#220c0c)}}body::-webkit-scrollbar{width:15px}body::-webkit-scrollbar-track{box-shadow:inset 0 0 6px rgba(0,0,0,.3)}body::-webkit-scrollbar-thumb{border-radius:15px;border:none;outline:0;background:#a8f268;background:linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-moz-linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-webkit-linear-gradient(90deg,#a8f268 0,#f7025c 100%)}@keyframes gradientAnimation_thumn{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}
/*body {overflow-x: hidden;}*/
/*.card, .main-footer, .navbar-white, .small-box>.inner, table{*/
/*    background-image: url("https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg");*/
/*        background-position: center;*/
/*        background-repeat: no-repeat;*/
/*        background-size: cover;*/
/*}*/
/*.main-footer, .navbar-white, .small-box>.inner, table{*/
.main-footer, .navbar-white{
    background-image: url("https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg");
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
}
.nmsr_block {
    padding: 35px 50px 35px 50px;
    border-radius: 10px;
    background: linear-gradient(145deg, #e2e8ec, #ffffff);
    box-shadow: 5px 5px 15px #D1D9E6, -5px -5px 15px #ffffff;
}
</style>
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "pcxay1h8bt");
    function setFavicon(url) {
    let link = document.querySelector("link[rel~='icon']");
    if (!link) {
        link = document.createElement('link');
        link.rel = 'icon';
        document.head.appendChild(link);
    }
    link.href = url;
}
setFavicon('https://stemapp.in/uploads/favicon/favicon.ico');
</script>

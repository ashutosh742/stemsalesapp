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
 <?php 
        $udetail    = $this->Menu_model->get_userbyid($uid);
        $ucash  = $udetail[0]->ucash;
        ?>
        <button type="button" class="btn btn-success"><span><b>Our Cash : <?=$ucash;?></b> <i class="fa fa-inr" aria-hidden="true"></i></span></button>
      
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      
    
          <?php 
                $cashlogsDetails = $this->Menu_model->GetLastCashLogByUser($uid);
            ?>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge"><?= sizeof($cashlogsDetails);?></span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3" style='max-width: 400px;'>
                   <?php 

                    if (!empty($cashlogsDetails)) {
                        foreach ($cashlogsDetails as $clog) {

                            if(in_array($clog->type,['debit','Deduct'])){

                                $cashMessage        = '';
                                $cashMessageColor   = 'text-danger';
                                $cashMessageSymbol  = '-';

                            }else if(in_array($clog->type,['Credit','Add'])){

                                $cashMessage        = '';
                                $cashMessageColor   = 'text-success';
                                $cashMessageSymbol  = '+';
                            }

                            $clog_created_at  = $clog->created_at;
                            $clog_av_cash  = $clog->av_cash;
                            
                            ?>
                           <p class="text-sm <?= $cashMessageColor ?>" style="font-weight:600;">
                                <?= $cashMessageSymbol ?> ₹ <?= $clog->cash ?>
                                — <?= htmlspecialchars($clog->remarks); ?> 💬
                                <span class='text-success'>— Available Balance: ₹ <?= $clog_av_cash; ?> </span>
                                <span class="text-sm text-muted">
                                    <i class="far fa-clock mr-1"></i> <?= $clog_created_at ?> ⏰ - <?= $clog->user_name ?> 
                                </span>
                            </p>
                            <hr>
                            <?php
                        }
                    }
                    ?>
                <!-- <a href="#" class="dropdown-item dropdown-footer">See All Messages</a> -->
            </div>
        </li>


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
    <center><h5 class="text-white"><b>STEM APP</b></h5></center>
    <hr>
    <!-- Sidebar -->

   <div class="user-panel mt-3 pb-3 mb-3 d-flex">

      <div class="image">

        <img src="<?=base_url();?>assets/image/user/Team.jpg" class="img-circle elevation-2" alt="User Image">

      </div>

      <div class="info">
      <?php $userName=$this->Menu_model->get_userName($uid);?>

        <a href="<?=base_url();?>Menu/myProfile" class="d-block"><?=$userName[0]->name?></a>

      </div>

    </div>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
            <li class="nav-item">
              <a href="<?=base_url();?>Menu/Dashboard" class="nav-link">
                <i class="fas fa-home nav-icon"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/DayManagement" class="nav-link">
                <i class="fas fa-calendar-day nav-icon"></i>
                <p> Day Management</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/GetAllCompaniesThatDoNotHavePrimaryContactDetailsData" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>No Primary Contact Companies</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/NewFunnel" class="nav-link">
                <i class="fas fa-filter nav-icon"></i>
                <p>New Funnel Added</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/UserTaskViewPage" class="nav-link">
                <i class="fas fa-tasks nav-icon"></i>
                <p>Todays Task Status</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/GetAllPlannerLogPlannedByUsers" class="nav-link">
                <i class="fas fa-sync-alt nav-icon"></i>
                <p>Todays Replanned Task</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/PendingForWriteMomMeetingList" class="nav-link">
                <i class="fas fa-edit nav-icon"></i>
                <p>Pending MOM Meeting List</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/AddSpecialCommentOnTask" class="nav-link">
                <i class="fas fa-comment-dots nav-icon"></i>
                <p>Add Special Comment On Task (Pending)</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/AddThanksCommentOnTask" class="nav-link">
                <i class="fas fa-thumbs-up nav-icon"></i>
                <p>Add Thanks Comment On Task (Complete)</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/SpecialRequestForLeaveSomeTime" class="nav-link">
                <i class="fas fa-user-clock nav-icon"></i>
                <p>Our Special Request For Leave Some Time</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/AllReviewPlaing" class="nav-link">
                <i class="fas fa-clipboard-check nav-icon"></i>
                <p>All Review Planning</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/StartAnnualReviews" class="nav-link">
                <i class="fas fa-calendar-check nav-icon"></i>
                <p>Start Annual Review</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>Menu/AllReviewPlaing" class="nav-link">
                <i class="fas fa-clipboard-check nav-icon"></i>
                <p>All Review Planning</p>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="<?=base_url();?>Menu/StartAnnualReviews">
                <i class="fas fa-calendar-check nav-icon"></i>
                <p>Start Annual Review</p>
              </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url();?>SalesReviews/index">
                      <i class="fas fa-calendar-check nav-icon"></i>
                    <p>BD Wise Sales Review</p>
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
                
            </ul>
            </li>






            <li class="nav-item">
            <a href="<?=base_url();?>Menu/pstnotassignc" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>PST Not Assign Company</p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/workdoneCompanyBDPST" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Work Done Company by BDPST</p>
            </a>
          </li>
         
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/NotworkCompanyBDPST" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Not Work Company by BDPST</p>
            </a>
          </li>
          
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/workbutactionnobd" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Work But Action No by BD</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/workbutactionnopst" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Work But Action No by PST</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/workOnClusterOnBDFunnels" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Work on Cluser of Bd Funnel</p>
            </a>
          </li>

         <li class="nav-item">
            <a href="<?=base_url();?>Menu/BDDayDetail/<?=$tdate?>/1" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Day Report</p>
            </a>
          </li>



          <li class="nav-item">
              <a href="<?=base_url();?>Menu/GetAllCompulsiveAndNeedYourAttentionLog" class="nav-link">
               <i class="fa-solid fa-hand-point-right"></i>
                <p>Compulsive Log</p>
              </a>
            </li>
          <li class="nav-item">
              <a href="<?=base_url();?>Menu/GetUserMarkedCurrentYearFocusFunnels" class="nav-link">
                <i class="fa-solid fa-hand-point-right"></i>
                <p> User Marked Current Year Focus Funnels </p>
              </a>
            </li>
          <li class="nav-item">
            <a href="<?=base_url();?>Menu/GetAllSameStatusLogByUserReports" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>All Same Status Log By User Reports</p>
            </a>
          </li>




          */ 
          
          ?>

          <!--<li class="nav-item">
            <a href="<?=base_url();?>Menu/LiveTaskTracking" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Live Task Tracking</p>
            </a>
          </li>-->
       
          <!--<li class="nav-item">
            <a href="<?=base_url();?>Menu/Mytarget" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>My Target</p>
            </a>
          </li>-->
          <!--<li class="nav-item">-->
          <!--  <a href="<?=base_url();?>Menu/AddFAQ" class="nav-link">-->
          <!--    <i class="far fa-circle nav-icon"></i>-->
          <!--    <p>Add FAQ</p>-->
          <!--  </a>-->
          <!--</li>-->
          
        

          <!-- <li class="nav-item">
            <a href="<?=base_url();?>Menu/ReviewDataReport" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>New Review Report</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="<?=base_url();?>Menu/cluster" >
              <i class="far fa-circle nav-icon"></i>
              <p>Add travel Cluster</p>
            </a>
          </li> -->



          <!--<li class="nav-item">-->
          <!--  <a class="nav-link" href="<?=base_url();?>Menu/AddSchoolDetails" >-->
          <!--    <i class="far fa-circle nav-icon"></i>-->
          <!--    <p>Add School Details</p>-->
          <!--  </a>-->
          <!--</li>-->
          
          <!-- <li class="nav-item">
           <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewDet" >
          <i class="far fa-circle nav-icon"></i>
              <p>Old Annual Review</p>
            </a>
          </li> -->


        

          <!-- <li class="nav-item">
              <a class="nav-link" href="<?=base_url();?>Menu/NewAnnualReviewReport" >
                  <i class="far fa-circle nav-icon"></i>
                  <p>New Annual Review Report</p>
              </a>
          </li> -->

          <!--<li class="nav-item">-->
          <!--  <a class="nav-link" href="<?=base_url();?>Menu/AnnualReviewReportData" >-->
          <!--    <i class="far fa-circle nav-icon"></i>-->
          <!--    <p>Annual Review Report</p>-->
          <!--  </a>-->
          <!--</li>-->
          
          <!-- <li class="nav-item">
            <a href="<?=base_url();?>Menu/NewLead" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>New Lead</p>
            </a>
           </li>  -->
           
           <!--<li class="nav-item">-->
           <!-- <a href="#" class="nav-link">-->
           <!--   <i class="far fa-circle nav-icon"></i>-->
           <!--   <p>Sales Kit</p>-->
           <!-- </a>-->
           <!--</li>-->
           <!--<li class="nav-item">-->
           <!-- <a href="<?=base_url();?>Menu/HumHongeTaiyar" class="nav-link">-->
           <!--   <i class="far fa-circle nav-icon"></i>-->
           <!--   <p>Hum Honge Taiyar</p>-->
           <!-- </a>-->
           <!--</li>-->
           <!--<li class="nav-item">-->
           <!-- <a href="<?=base_url();?>Menu/HumHongeTaiyarReport" class="nav-link">-->
           <!--   <i class="far fa-circle nav-icon"></i>-->
           <!--   <p>Hum Honge Taiyar Report</p>-->
           <!-- </a>-->
           <!--</li>-->




           <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Travel Adavance
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurTravelAdvanceRequest" class="nav-link">
                          <i class="fa-solid fa-hand-point-right"></i>
                              <p>Cash Advance Request</p>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurCashExpenseReport" class="nav-link">
                             <i class="fa-solid fa-hand-point-right"></i>
                              <p>Cash Expense Request</p>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a href="<?=base_url();?>Menu/UpdateTodaysMeetingsDetails" class="nav-link">
                              <i class="fa-solid fa-hand-point-right"></i>
                              <p>Update Meetings Expense Details</p>
                            </a>
                          </li>
                          <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurTeamTravelAdvanceReport" class="nav-link">
                            <i class="fa-solid fa-hand-point-right"></i>
                                <p>Our Travel Advance Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?=base_url();?>Menu/OurTeamCashExpenseReport" class="nav-link">
                            <i class="fa-solid fa-hand-point-right"></i>
                                <p>Our Cash Expense Report</p>
                            </a>
                        </li>
                    </ul>
                </li> -->




               
      <!-- <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                    Add Target 
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
                </li> -->
              

        <li class="nav-item">
          <a href="<?=base_url();?>Menu/logout" class="nav-link">
            <i class="fas fa-sign-out-alt nav-icon"></i>
            <p> Logout</p>
          </a>
        </li>

          
        </ul>
       </nav>
      <!-- /.sidebar-menu -->
      
       <?php 

      $daycheckDatas = $this->Menu_model->get_daystarted($user['user_id'],date("Y-m-d"));
      if(sizeof($daycheckDatas) > 0){
      $getcurUrl = $this->Menu_model->getCurrentUrl();
      $checkSelfAssignRequest     = $this->Menu_model->GetOurSelfAssignRequestByUser($uid,date("Y-m-d"));
      if(sizeof($checkSelfAssignRequest) > 0){
          $checkonUrl2 = base_url().'Menu/UserTaskViewPage';

          if($getcurUrl !== $checkonUrl2){
              if (strpos($getcurUrl, 'selfTaskAssignPage') !== false) {
              } else {
                  $checkSelfAssignRequestcnt = sizeof($checkSelfAssignRequest);
                  $this->load->library('session');
                  $this->session->set_flashdata('error_message', " * Your Line Manager has created $checkSelfAssignRequestcnt task with a Self-Assign request. Please review and assign the task at your earliest convenience.");
                  redirect('Menu/UserTaskViewPage');
              }
            }
      }
    }
      ?>
      
      <hr>
        <a href="AlertsPage"><button type="button" class="btn btn-danger">
            Alerts! <span class="badge badge-light">100</span>
        </button></a>
      <hr>
        <a href="NotificationPage"><button type="button" class="btn btn-success">
            Notification! <span class="badge badge-light">52</span>
        </button></a>
      <hr>
      
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
      <?php 

      // $this->Menu_model->CheckUserPlannerStartOrNot($current_uid,date("Y-m-d"));
       ?>

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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script> 
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PEGQELMXKQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PEGQELMXKQ');
</script>
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
<script>
    
$(document).ready(function() {
    trackLocation();
    GetTodaysReviewPlan();
    // CheckPendingAnnualReview();
    // CheckPendingToSetTargetDataAfterReviewClosed();
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


 function getAddress(lat, lon) {
                // Use Nominatim API for reverse geocoding
                var apiUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`;

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(data) {
                        if (data.display_name) {
                            $('#useraddress').text(data.display_name);
                        } else {
                            $('#useraddress').text("No address found for the given coordinates.");
                        }
                    },
                    error: function(error) {
                        $('#address').text("Error fetching address.");
                    }
                });
            }

function trackLocation() {
    if ("geolocation" in navigator) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          var ur_id = document.getElementById("ur_id").value;
          var latitude = position.coords.latitude;
          var longitude = position.coords.longitude;
          getAddress(latitude, longitude);
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
    // else {console.error("Geolocation is not supported by this browser.");} 
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
    
    
    // $.ajax({
    // url:'<?=base_url();?>Menu/opalsms',
    //  method: 'post',
    //  data: {ur_id: ur_id},
    //  success: function(result){
    //     var res = result;
    //     $("#opalsms").html(result);
    // }
    // });
    
    
    // $.ajax({
    // url:'<?=base_url();?>Menu/nitisms',
    //  method: 'post',
    //  data: {ur_id: ur_id},
    //  success: function(result){
    //     var res = result;
    //     $("#nitisms").html(result);
    // }
    // });


function GetTodaysReviewPlan() {
      
      $.ajax({
          url: '<?=base_url();?>Menu/GetTodaysPlanedReview',
          type: "GET",
          cache: false,
          success: function (result) {
            
              let currentLocation = window.location.toString();
              var jsonArray = JSON.parse(result);
              var dataValue = jsonArray.data;
              var redirect  = jsonArray.redirect;
              var revpage   = jsonArray.revpage.toString();
              var rev_uid   = jsonArray.rev_uid;
              var rev_bdid  = jsonArray.rev_bdid;
              var by_type_id = jsonArray.by_type_id;
              var to_type_id = jsonArray.to_type_id;
              var cur_type_id = jsonArray.cur_type_id;
              if(currentLocation !== revpage){
              let timerInterval;
              Swal.fire({
              title: "<u>Review Planned By</u>",
              html: dataValue,
              timer: 10000,
              timerProgressBar: true,
              didOpen: () => {
                  Swal.showLoading();
                  const timer = Swal.getPopup().querySelector("b");
                  timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                  }, 100);
              },
              willClose: () => {
                  clearInterval(timerInterval);
              }
              }).then((result) => {
              if (result.dismiss === Swal.DismissReason.timer) {
                  if(rev_uid !== rev_bdid){
                      if(cur_type_id == 13){
                          if(by_type_id == 13 && to_type_id == 3){
                            window.location.replace(revpage);
                          }else if(by_type_id == 4 && to_type_id == 13){
                            window.location.replace(redirect);
                          }else if(by_type_id == 13 && to_type_id == 13){
                            window.location.replace(revpage);
                          }
                      }else if(cur_type_id == 4){
                        if(by_type_id == 4 && to_type_id == 13){
                            window.location.replace(revpage);
                          }else if(by_type_id == 4 && to_type_id == 3){
                            window.location.replace(revpage);
                          }else if(by_type_id == 4 && to_type_id == 4){
                            window.location.replace(revpage);
                          }
                      }else if(cur_type_id == 3){
                        if(by_type_id == 13 && to_type_id == 3){
                            window.location.replace(redirect);
                          }else if(by_type_id == 4 && to_type_id == 3){
                            window.location.replace(redirect);
                          }else if(by_type_id == 3 && to_type_id == 13){
                            window.location.replace(revpage);
                          }
                      } 
                  }
              }
              });
              }
          }
      });
  }


  function CheckPendingAnnualReview() {
      
      $.ajax({
          url: '<?=base_url();?>Menu/CheckPendingAnuualRevirewCmpOnReviewPage',
          type: "GET",
          cache: false,
          success: function (result) {
            console.log("CheckPendingAnnualReview is Runnig = ");
            results =  Number(result);
            if(results > 0){
              let currentLocation = window.location.toString();
              let redirectreviewpage = "https://stemapp.in/Menu/StartAnnualReviews";
              let redirectDayManagement = "https://stemapp.in/Menu/DayManagement";
              if(currentLocation !== redirectreviewpage){
                if(currentLocation !== redirectDayManagement){
                  alert("* Please complete your Annual Review first.");
                  // window.location.href = "https://stemapp.in/Menu/StartAnnualReviews";
                 }
              }
            }
          }
      });
  }



  function CheckPendingToSetTargetDataAfterReviewClosed() {
      
      $.ajax({
          url: '<?=base_url();?>Menu/CheckPendingToSetTargetDataAfterReviewClosedPage',
          type: "GET",
          cache: false,
          success: function (result) {
            results =  Number(result);
            // console.log(results +" target settings were found pending after the review was closed. Please complete the target settings first.");
            // var addTargetPage1 = "https://stemapp.in/Menu/AddTarget/280/";
            // var addTargetPage2 = "https://stemapp.in/Menu/AddTarget";

            // if(results > 0){
            //   let currentLocation       = window.location.toString();
            //   let redirectreviewpage    = "https://stemapp.in/Menu/PendingTargetDetailsAfterReviewByUser";
            //   let redirectDayManagement = "https://stemapp.in/Menu/DayManagement";
            //   let redirectDashboard2    = "https://stemapp.in/Menu/Dashboard2";
            //     if(currentLocation !== redirectreviewpage && currentLocation !== redirectDayManagement && currentLocation !== redirectDashboard2 && !addTargetPage1.includes(addTargetPage2)){
            //       console.log(results +" target settings were found pending after the review was closed. Please complete the target settings first.");
            //       console.log("Matched");
            //      }else{
            //       console.log("Not Matched");
            //      }
            // }
          }
      });
  }



</script>
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "pcxay1h8bt");


    $(document).ready(function() {
            let leaveTime = null;
            let wasHidden = false;
            let openTime = new Date();
            let lastEventId = null;

            console.log('Page opened at ' + openTime);
            fireAjaxRequest('open', openTime);

            $(window).on('blur', function() {
                leaveTime = new Date();
                console.log('You left the window at ' + leaveTime);
                fireAjaxRequest('leave', leaveTime);
            });

            $(window).on('focus', function() {
                if (leaveTime) {
                    const returnTime = new Date();
                    console.log('You returned to the window at ' + returnTime);
                    fireAjaxRequest('return', returnTime);
                    leaveTime = null;
                }
            });

            $(document).on('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    if (wasHidden) {
                        console.log('Welcome back!');
                        wasHidden = false;
                    }
                    const returnTime = new Date();
                    console.log('Tab became visible at ' + returnTime);
                    fireAjaxRequest('visible', returnTime);
                } else {
                    leaveTime = new Date();
                    console.log('You left the tab at ' + leaveTime);
                    fireAjaxRequest('hidden', leaveTime);
                    wasHidden = true;
                }
            });

            $(window).on('beforeunload', function() {
                const closeTime = new Date();
                console.log('Page closed at ' + closeTime);
                fireAjaxRequest('closed', closeTime);
            });

            function getCurrentUrlPath() {
                return window.location.href;
            }

            // function fireAjaxRequest(eventType, eventTime) {
            //     $.ajax({
            //         url: '<?=base_url();?>Menu/trackActivity', // Replace with your server endpoint
            //         method: 'POST',
            //         data: {
            //             eventType: eventType,
            //             eventTime: eventTime.toISOString().slice(0, 19).replace('T', ' '), // Format datetime
            //             urlPath: getCurrentUrlPath(),
            //             lastEventId: lastEventId
            //         },
            //         success: function(response) {
            //             // console.log('AJAX request successful:', response);
            //             try {
            //                 const data = JSON.parse(response);
            //                 if (data.eventId !== undefined) {
            //                     lastEventId = data.eventId;
            //                 }
            //             } catch (error) {
            //                 console.error('Error parsing JSON response:', error);
            //             }
            //         },
            //         error: function(error) {
            //             console.error('AJAX request failed:', error);
            //         }
            //     });
            // }
        });

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

<style>
.bg-light,.bg-light>a,.card.card-outline-tabs{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2)}.card,.card.card-outline-tabs,.small-box>.inner,table{transition:box-shadow .2s ease-in-out;padding:10px;background:#fff8dc}.card.card-outline-tabs{border-top:0}.bg-light,.bg-light>a{border-radius:0;transition:box-shadow .2s ease-in-out}.card{box-shadow:none}.card,.main-footer,.navbar-white,.small-box>.inner,table{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2)}.navbar-white{background:#fff8dc}[class*=sidebar-dark-]{background:#3b1818;background:conic-gradient(from 180deg at 50% 50%,#220c0c,#220c0c,#0f0c22,#0c2215,#140c22,#220c13);animation:20s linear infinite spinGradient;transition:.2s ease-in-out}.nav-sidebar>.nav-item{box-shadow:rgba(60,64,67,.3) 0 1px 2px 0,rgba(60,64,67,.15) 0 2px 6px 2px}.fa-circle:before,.nav-sidebar .nav-item>.nav-link{font-size:13px;margin:-2px}#alsmss,span#alsmss{font-size:13px}#alsmss h5{font-size:15px}aside #mainlogo{filter:drop-shadow(0 0 1rem blue);animation:5s linear infinite spinGradient_image;transition:.2s}@keyframes spinGradient_image{0%{filter:drop-shadow(0 0 1rem #ff0f7b)}25%{filter:drop-shadow(0 0 1rem #fff95b)}50%{filter:drop-shadow(0 0 1rem #e60b09)}75%{filter:drop-shadow(0 0 1rem #59d102)}100%{filter:drop-shadow(0 0 1rem #6bdfdb)}}@keyframes spinGradient{0%{background:conic-gradient(from 90deg at 50% 50%,#220c0c,#220c0c,#0f0c22,#0c2215,#140c22,#220c13)}25%{background:radial-gradient(circle,#220c0c,#0f0c22,#0c2215,#140c22,#220c13)}100%,50%{background:conic-gradient(from 180deg at 50% 50%,#220c0c,#0f0c22,#0c2215,#140c22,#220c13,#220c0c)}75%{background:conic-gradient(from 270deg at 50% 50%,#220c0c,#0f0c22,#0c2215,#140c22,#220c13,#220c0c)}}body::-webkit-scrollbar{width:15px}body::-webkit-scrollbar-track{box-shadow:inset 0 0 6px rgba(0,0,0,.3)}body::-webkit-scrollbar-thumb{border-radius:15px;border:none;outline:0;background:#a8f268;background:linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-moz-linear-gradient(90deg,#a8f268 0,#f7025c 100%);background:-webkit-linear-gradient(90deg,#a8f268 0,#f7025c 100%)}@keyframes gradientAnimation_thumn{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}
/*.card, .main-footer, .navbar-white, .small-box>.inner, table {*/
/*    background-image: url("https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg");*/
/*        background-position: center;*/
/*        background-repeat: no-repeat;*/
/*        background-size: cover;*/
/*}*/
.main-footer, .navbar-white,.card-body-addnewlead {
    background-image: url("https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg");
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
}
.card-body-addnewlead {
    box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15), inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
    transition: box-shadow 0.2s ease-in-out;
    border-radius: 20px;
}
.card-body-addnewlead:hover {
    color: #000000!important;
}
</style>

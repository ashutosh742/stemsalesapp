<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STEM Oppration | WebAPP</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
    <style>
      .scrollme {
      overflow-x: auto;
      }
      .form-group {
      margin-bottom: 1rem;
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15), inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
      transition: box-shadow 0.2s ease-in-out;
      padding: 10px;
      }
      .card-body, .card {
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15), inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
      transition: box-shadow 0.2s ease-in-out;
      }
      .newcentercls{
      align-items: center;
      justify-content: center;
      display: flex;
      background: gainsboro;
      }
      .form-control {
      background: beige;
      }
      .select2-container--default .select2-selection--multiple .select2-selection__rendered li:first-child.select2-search.select2-search--inline .select2-search__field {
      background: beige;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <style>
        .card{
          background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;
        }
      </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
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
            <?php
              if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">

                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h3><i>📌 Task Assign</i></h3> 
                <small>🧑‍💼 Assigned tasks per user, sorted by team or personal role. 🔍 Track task progress, responsibility, and follow-ups with clear visibility. 📋✅</small>
                  </div>
                  
                  <hr>

                  
                    <form action="<?=base_url();?>Menu/dailyTaskAssign" method="post">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>👤 Select Role</label>
                            <select class="custom-select rounded-0" name="role" id="role" >
                              <option>Select Role</option>
                              <?php 
                                foreach($role as $r){
                                  $slcttypeid = $r->id;
                                  if($user['type_id'] == 2){
                                    if(!in_array($slcttypeid,[3,4,13,24,19,20,21,22,23,24])){
                                      continue;
                                    }
                                  }elseif($user['type_id'] == 15 || $user['type_id'] == 19 || $user['type_id'] == 20 || $user['type_id'] == 21 || $user['type_id'] == 22 || $user['type_id'] == 23){
                                    if(!in_array($slcttypeid,[3,4,13,24])){
                                      continue;
                                    }
                                  }elseif($user['type_id'] == 4){
                                    if(!in_array($slcttypeid,[3,13,24])){
                                      continue;
                                    }
                                  }elseif($user['type_id'] == 13){
                                    if(!in_array($slcttypeid,[3,24])){
                                      continue;
                                    }
                                  }elseif($user['type_id'] == 24){
                                    if(!in_array($slcttypeid,[3])){
                                      continue;
                                    }
                                  }
                                  ?>
                              <option value="<?=$r->id?>"> <?=$r->name?></option>
                              <?php } ?>
                            </select>
                            <small>
                              🎭 Select Role
Choose the appropriate role for the individual or task. 👤 This defines responsibilities, access levels, and accountability in the process. 🛠️ Roles may include BD, PST, Reviewer, or Coordinator — helping streamline workflow and ensure clarity in execution. ✅ Proper role assignment boosts efficiency and teamwork! 🤝📋
                            </small>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>🧑 Select User</label>
                            <select id="user" class="form-control" name="user">
                            </select>
                          </div>
                          <small>
                            👤 Select User
Pick a specific user to assign the task or responsibility. 📌 This ensures accountability and clear ownership of actions. Ideal for delegating follow-ups, tracking performance, and managing workflows efficiently. ✅ Choose wisely to align expertise with the task’s needs. 🚀📈
                          </small>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>🏙️ City <span class="citycount"  style="font-size:12px; padding-left:10px"></span> <span class="showcities" style="color:red;font-size:12px; padding-left:10px"> (If you did not select city then all cities will be selected for above user)<span></label>
                            <select id="city" class="form-control select2" name="city[]"  multiple="multiple">
                              <option value="">All </option>
                            </select>
                            <small>
                              🏙️ Select City
Choose the relevant city to filter tasks, assign leads, or plan activities based on geographic location. 📍 This helps in regional tracking, resource allocation, and cluster-based strategy execution. 🌐 Ensures localized follow-ups and better coordination with on-ground teams. ✅
                            </small>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>🤝 Partner<span class="partnercount"style="font-size:12px; padding-left:10px"></span><span class="showpartner" style="color:red;font-size:12px; padding-left:10px"> (If you did not select partner then all partner will be selected for above user and cities)<span></label>
                        <select id="partner" class="form-control select2" name="partner[]" multiple="multiple">
                          <!-- <option value="">All </option> -->
                        </select>
                        <small>
                          🤝 Select Partner
Choose the appropriate partner associated with the task or company. 🧩 This helps in identifying collaboration channels, tracking partner performance, and ensuring joint accountability. 🔗 A correct partner selection supports strategic alignment and smooth coordination. ✅ Strengthen partnerships for better outcomes! 📈🌟
                        </small>
                      </div>
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>⏮️ Earlier Status</label>
                            <select id="previous_status" class="form-control" name="previous_status">
                              <option value="">Select Status</option>
                              <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                              <option value="<?=$status->id?>"><?=$status->name?></option>
                              <?php } ?>
                            </select>
                          <small>
                            🕓 Select Earlier Status
Choose the previous status to review progress or identify changes. 🔄 This helps in tracking task evolution, analyzing delays, and ensuring proper follow-up. 📊 Comparing earlier and current status gives insights into performance trends and bottlenecks. 🔍 Stay informed, act accordingly! ✅
                          </small>
                          </div>
                          
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>🟢 Current Status </label>
                            <select id="current_status" class="form-control" name="current_status" required>
                              <!-- <option value="">All</option> -->
                              <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                              <option value="<?=$status->id?>"><?=$status->name?></option>
                              <?php } ?>
                            </select>
                            <small>
                              ⚡ Select Current Status
Pick the present status of the task or company to reflect its latest progress. ✅ This is essential for accurate tracking, reporting, and decision-making. Keeping the current status updated ensures transparency and helps prioritize next steps effectively. 📈 Stay on top of updates! 🕒
                            </small>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>📆 Days</label>
                            <select id="days" class="form-control" name="days">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                            </select>
                            <small>
                              📅 Select Days
Choose the number of days to filter or set timelines for tasks and activities. ⏳ This helps in managing deadlines, scheduling follow-ups, and monitoring progress within specific day ranges. ✅ Perfect for short-term planning and timely execution! 🕒
                            </small>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <!-- <div class="col-4">
                          <div class="form-group">
                            <input type="radio" id="option1" name="optradio" value="taskDone">
                                                            <label>Task Done</label>
                          </div>
                          </div>
                          <div class="col-4">
                          <div class="form-group">
                            <input type="radio" id="option2" name="optradio" value="noTaskDone">
                                                          <label>No Task Done </label>                    
                          </div>
                          </div> -->
                        <div class="col-6">
                          <div class="form-group">
                            <input type="radio" id="option1" name="optradio" value="actionPlanned">
                            <label>📝 Action Planned</label>
                            <br>
                            <small>
                              📝 Action Planned
A clear action plan has been outlined to move the task forward. 🎯 This includes specific steps, deadlines, and responsible persons to ensure progress. ✅ Well-defined actions help in tracking accountability and achieving desired results efficiently. 🚀 Keep the plan updated and follow through!
                            </small>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="form-group">
                            <input type="radio" id="option2" name="optradio" value="actionNotPlanned">
                            <label>⚠️ Action Not Planned </label> 
                            <br>
                            <small>
                              ⚠️ Action Not Planned
No specific actions have been scheduled yet for this task. 🛑 This indicates a need for immediate planning to avoid delays and ensure progress. 🔄 Prioritize defining clear next steps and assigning responsibilities to move forward effectively. ⏳ Don’t let tasks stall!
                            </small>                         
                          </div>
                        </div>
                        <!-- <div class="col-4">
                          <div class="form-group">
                          <label>Days</label>
                            <select id="days" class="form-control" name="days">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                              </select>
                            
                          </div>
                          </div> -->
                      </div>
                      <div class="row actionpurpose">
                        <div class="col-4">
                          <div class="form-group">
                            <label>✅ Action Taken: Yes / No </label>
                            <select id="action" class="form-control" name="action">
                              <!-- <option value="">All</option> -->
                              <option value="yes">Yes</option>
                              <option value="no">No</option>
                            </select>
                            <small>
                              ✔️❌ Action Taken: Yes / No
Indicates whether a specific action has been completed (Yes) or not (No). ✅ This helps track progress and accountability clearly. Timely actions lead to successful outcomes, while “No” signals the need for follow-up or intervention. Stay on top of action statuses! 📋
                            </small>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>✅ Purpose Achieved: Yes / No</label>
                            <select id="purpose" class="form-control" name="purpose">
                              <!-- <option value="">All </option> -->
                              <option value="yes">Yes</option>
                              <option value="no">No </option>
                            </select>
                            <small>
                              🎯 Purpose Achieved: Yes / No
Indicates whether the intended goal or objective has been successfully met (Yes) or not (No). ✅ A “Yes” reflects successful completion, while “No” highlights the need for further action or improvement. Tracking this ensures focus on meaningful results. Keep goals clear and measurable! 📊
                            </small>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>📆 Days</label>
                            <select id="daysbyplandate" class="form-control" name="daysbyplandate">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                            </select>
                            <small>
                              📅 Days
Represents the number of days related to a task or event — such as duration, deadline, or days elapsed. ⏳ Tracking days helps manage schedules, monitor progress, and ensure timely completion. Stay aware of timelines to keep everything on track! ✅
                            </small>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-6">
                          <div class="form-group">
                            <label>🗂️ Task / 🛠️ Action</label>
                            <select id="task_action" class="form-control" name="task_action">
                              <option value="">Select Task</option>
                              <?php $action = $this->Menu_model->get_action();
                                foreach($action as $a){
                                ?>
                              <option value="<?=$a->id?>"><?=$a->name?></option>
                              <?php } ?>
                            </select>
                            <small>
                              🛠️ Task / Action
This refers to the specific task or action item that needs to be completed. 🎯 Clear definition of the task helps in focused execution and accountability. ✅ Tracking each action ensures steady progress and timely achievement of goals. Keep tasks well-organized and updated! 📋✨
                            </small>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="form-group">
                            <label>📆 Days by Task</label>
                            <select id="daysbyplandatewithtask" class="form-control" name="daysbyplandatewithtask">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                            </select>
                            <small>
                              📅 Days by Task
Tracks the number of days spent or allocated for each specific task. ⏳ This helps measure task duration, monitor delays, and manage timelines effectively. 📈 Useful for performance analysis and ensuring timely completion of activities. Keep an eye on days spent to stay on schedule! ✅
                            </small>
                          </div>
                        </div>
                      </div>
                      <!-- <div class="form-group">
                        <input type="radio" id="workbyothers" name="workbyothers" value="workbyothers">
                                                          <label>Work by Others</label>
                        </div> -->
                      <div class="form-group">
                        <label>👥 Work by Others</label>
                        <select id="workbyothers" class="form-control" name="workbyothers">
                          <option value="">Select</option>
                          <option value="" id="op1"></option>
                          <option value="admin">Admin</option>
                        </select>
                        <small>
                          🤝 Work by Others
Tasks or activities carried out by team members or collaborators apart from yourself. Tracking this ensures visibility of contributions, fosters teamwork, and helps coordinate efforts efficiently. ✅ Recognizing and managing work done by others keeps projects on track and boosts collective success.
                        </small>
                      </div>
                      <div class="form-group">
                        <label>🗂️ Category</label>
                        <select id="category" class="form-control" name="category">
                          <option value="">All</option>
                          <option value="topspender">Top Spender</option>
                          <option value="upsell_client">Upsell Client</option>
                          <option value="upsell_client">Upsell Client</option>
                          <option value="focus_funnel">Focus Funnel</option>
                          <option value="keycompany">Key Company</option>
                          <option value="pkclient">P Key Client</option>
                        </select>
                        <small>
                         📂 Category
A classification or grouping assigned to a task, company, or activity. 🗂️ Categorizing helps organize data, streamline filtering, and prioritize work based on type or importance. ✅ Proper categorization improves clarity and makes management easier. Keep categories clear and consistent!
                        </small>
                      </div>
                      <div class="card p-2">
                        <div class="row">
                          <div class="col-12 text-center">
                            <div class="form-group">
                              <!-- <input type="radio" id="meetingDone" name="meetingDone" value="after_meeting_Done"> -->
                              <label>&nbsp;&nbsp;🎯✅ Meeting Done : &nbsp;&nbsp;&nbsp;</label>
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="yes" name="meetingDone">Yes
                                </label>
                              </div>
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="no" name="meetingDone">No
                                </label>
                              </div>
                              <br>
                               <small>
                              ✅ Meeting Done
The scheduled meeting has been successfully completed. 🗓️🤝 Key points were discussed, and action items may have been identified. This marks progress in the engagement and should be followed by timely execution and updates. 📝📌 Keep momentum going post-meeting! 🚀
                            </small>
                            </div>
                          
                          </div>
                          <div class="col-6 text-center">
                            <div class="form-group">
                              <label>&nbsp;&nbsp;🧾 PST Assign : &nbsp;&nbsp;&nbsp;</label>
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="yes" name="pst_assign">Yes
                                </label>
                              </div>
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="no" name="pst_assign">No
                                </label>
                              </div>
                              <br>
                            <small>
                              🧩 PST Assign
A Product Specialist Team (PST) has been assigned to this task. 👥🔧 They will provide expert guidance, support product-related discussions, and help drive solutions. This is a crucial step toward deepening engagement and moving the opportunity forward. 📈✅ Stay aligned with PST for effective outcomes. 🎯
                            </small>
                            </div>
                          </div>
                          <div class="col-6 text-center">
                            <div class="form-group">
                              <label>&nbsp;&nbsp;📍✅ Cluster Assign : &nbsp;&nbsp;&nbsp;</label>
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="yes" name="cluster_assign">Yes
                                </label>
                              </div>
                              <div class="form-check-inline">
                                <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="no" name="cluster_assign">No
                                </label>
                              </div>
                              <br>
                              <small>
                                📍 Cluster Assign
The company or task has been assigned to a specific cluster based on location or category. 🗺️📦 This helps in streamlined coordination, targeted follow-ups, and efficient resource allocation. 🔄 Ensures that efforts are localized and aligned with regional strategies. ✅ Stay connected with the assigned cluster lead! 🤝
                              </small>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="card">
                              <div class="form-group">
                              <input type="radio" id="anpnlastweek" name="manadatoryfilter" value="anpnlastweek">
                              <label>&nbsp;&nbsp;📅 AN/PN Last Week</label> <br>
                            </div>
                             <small>🔴 No Action, No Purpose Achieved Last Week
                              Despite the plan being active (AN/PN), no concrete steps were taken, and the intended outcomes were not realized. ⚠️ This indicates a delay or lack of execution in critical tasks. Immediate attention is required to realign focus and ensure timely progress. 📉🛑</small>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="card">
                              <div class="form-group">
                              <input type="radio" id="will_give_business" name="manadatoryfilter" value="will_give_business">
                              <label>&nbsp;&nbsp;🤝 Will Give Business </label>                          
                            </div>
                            <small>
                              💼 Will Give Business
The company has shown strong intent to provide business soon. 🤝 Their interest is confirmed, and discussions are moving positively. 📈 This is a promising opportunity that requires regular follow-up and relationship building to ensure successful conversion. 🌟✅
                            </small>
                            </div>
                          </div>
                          <div class="col-3">
                           <div class="card">
                             <div class="form-group">
                              <input type="radio" id="no_status_changes" name="manadatoryfilter" value="No Status Changes">
                              <label>&nbsp;&nbsp;🔄❌ No Status Changes</label>                          
                            </div>
                            <small>
                              ⏸️ No Status Changes
There has been no update or movement in the task or company status. 📋 The situation remains unchanged from the previous review. 🔄 Continued monitoring and timely follow-ups are essential to avoid stagnation and ensure progress. 🕵️‍♂️🔁
                            </small>
                           </div>
                          </div>
                          <div class="col-3">
                           <div class="card">
                             <div class="form-group" title="📅 Review Date & 🎯 Target Status (Achieved/Not Achieved)">
                              <input type="radio" id="review_time_target" name="manadatoryfilter" value="Review Date with target status achived or not">
                              <label>&nbsp;&nbsp;📅 Review 🎯 Status: Achieved / Not Achieved</label>                          
                            </div>
                            <small>
                              📅 Review Date & 🎯 Target Status (Achieved/Not Achieved)
This section highlights the scheduled review date along with the goal status. If 🎯 Achieved, it reflects timely execution and successful outcomes. ✅ If Not Achieved, it indicates delays or unmet objectives, requiring immediate corrective action. ⚠️ Essential for tracking accountability and driving performance. 📊
                            </small>
                           </div>
                          </div>

                          <div class="col-12 text-center pb-3">
                            <hr>
                            <center>
                              <label>📅 Select Week</label>
                              <select class="form-control" id="select_week" style="width:50%">
                                <option value="">Select</option>
                                <option value="1">1 Week</option>
                                <option value="2">2 Week</option>
                                <option value="3">3 Week</option>
                                <option value="4">4 Week</option>
                                <option value="5">5 Week</option>
                                <option value="6">6 Week</option>
                                <option value="7">7 Week</option>
                                <option value="8">8 Week</option>
                                <option value="9">9 Week</option>
                                <option value="10">10 Week</option>
                              </select>
                            </center>
                            <small>
                              📆 Select How Many Weeks
Choose the number of weeks to filter or plan your tasks. 🔢 This helps in tracking progress over a custom time range — whether it’s 1️⃣, 2️⃣, or several weeks. Ideal for reviewing short-term goals, weekly follow-ups, or forecasting timelines. 📈✅
                            </small>
                            <hr>
                          </div>

                        </div>
                      </div>


                      <hr>
                      <div class="row">
                        <div class="col-md-4">
                                   <div class="card p-4 form-group" id="new_category_filter_card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;min-height:200px;justify-content: center;">
                                    <label>🆕 New Category</label>
                                    <select id="new_category_filter_select" class="form-control" name="new_category_filter_select">
                                      <option value="">Select New Category</option>
                                      <option value="Q1 20 Closure Funnel">Q1 20 Closure Funnel</option>
                                      <option value="Potential Funnel For FY">Potential Funnel For FY</option>
                                      <option value="To be Nurtured for FY">To be Nurtured for FY</option>
                                      <option value="50 New Lead Funnel">50 New Lead Funnel</option>
                                      <option value="BD Marked Business Potential">Marked Business Potential</option>
                                      <option value="Anchor Clients">Anchor Clients</option>
                                    </select>
                                    <hr>
                                    <small>
                                      🎯 This dropdown allows users to filter leads by their business funnel classification.<br><br>
                                      Available options include:<br>
                                      📌 Q1 20 Closure Funnel<br>
                                      📈 Potential Funnel for FY<br>
                                      🌱 To Be Nurtured for FY<br>
                                      🧲 50 New Lead Funnel<br>
                                      💼 BD Marked Business Potential<br>
                                      🧭 Anchor Clients<br><br>

                                      It enables better lead segmentation based on current status or business potential, helping prioritize, track, and plan follow-up actions effectively.
                                    </small>

                                  </div>
                        </div>
                        <div class="col-md-4">
                              <div class="form-check" id="current_quarter_filter" title="Marked In Current Quarter refers to activities, deals, or reviews that have been recorded or scheduled within the ongoing financial quarter. This helps in tracking quarterly targets, performance metrics, and ensuring that all relevant actions are aligned with current reporting and strategic timelines.">
                                <div class="card p-4 form-group" id="current_quarter_filter_card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;min-height:200px;justify-content: center;">
                                  <?php 
                                    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                                    $currentQuarterNumber = $cfData['quarter'];
                                    $currentQuarter = "Q".$currentQuarterNumber;
                                    $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
                                    $start_financial_date   = $curFinancialDate['start_date'];
                                    $start_financial_date_year  = new DateTime($start_financial_date);
                                    $cfyear                       = $start_financial_date_year->format('Y');
                                  ?>
                                  <label>📌 Marked In Quarter <?=$currentQuarterNumber;?> in <?=$cfyear;?></label>
                                  <select id="current_quarter_filter_select" class="form-control" name="current_quarter_filter_select">
                                    <option value="">Select Category in Current Quarter <?=$currentQuarterNumber;?> in <?=$cfyear;?></option>
                                    <option value="20 Closure Funnel in <?=$currentQuarter;?>">20 Closure Funnel	in <?=$currentQuarter;?></option>
                                    <option value="Potential Funnel For <?=$currentQuarter;?>">Potential Funnel For <?=$currentQuarter;?></option>
                                    <option value="To be Nurtured for <?=$currentQuarter;?>">To be Nurtured for <?=$currentQuarter;?></option>
                                  </select>
                                  <hr>
                                  <small>
                                  📅 The dropdown labeled  
                                  <b>"Marked In Quarter <?=$currentQuarterNumber;?> in <?=$cfyear;?>"</b>  
                                  allows users to filter leads based on their current status in <b><?=$currentQuarter;?></b>.<br><br>

                                  Available options include:<br>
                                  📌 20 Closure Funnel in <?=$currentQuarter;?><br>
                                  📈 Potential Funnel for <?=$currentQuarter;?><br>
                                  🌱 To Be Nurtured for <?=$currentQuarter;?><br><br>

                                  This helps categorize leads for Q<?=$currentQuarterNumber;?>, enabling better progress analysis, follow-up prioritization, and strategic planning for conversions and nurturing within the specified quarter.
                                </small>

                                </div>
                              </div>
                        </div>
                        <div class="col-md-4">
                           <div class="card p-4 form-group" id="closing_timeline_filter_card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;min-height:200px;justify-content: center;">
                              <label>🔽 Select Status</b> (⏳ Closing Timeline on 🟡 Tentative / 🟢 Positive)</label>
                              <select id="closing_timeline_filter_select_status" class="form-control" name="closing_timeline_filter_select_status">
                                <option value="">Select Status</option>
                                <option value="3">Tentative</option>
                                <option value="6">Positive</option>
                              </select>
                              
                              <div id="propose_or_clarify_filter_select_card">
                                <hr>
                                <label>Propose or Clarify Timeline</label>
                                <select class="form-control" id="propose_or_clarify_filter_select">
                                    <option value="Propose	Day 6 to 8">Propose	Day 6 to 8</option>
                                    <option value="Clarify	Day 8 to 12">Clarify	Day 8 to 12</option>
                                    <option value="Nudge Day 12 to 14">Nudge Day 12 to 14</option>
                                    <option value="Support	Day 15 to 18">Support	Day 15 to 18</option>
                                </select>
                              </div>

                              <div id="closing_timeline_filter_select_card">
                                <hr>
                                  <label>⏳ Closing Timeline</label>
                                  <select class="form-control" id="closing_timeline_filter_select">
                                      <option value="In 8 Days">In 8 Days</option>
                                      <option value="In 8 to 15 Days">In 8 to 15 Days</option>
                                      <option value="In 15 to 30 Days">In 15 to 30 Days</option>
                                      <option value="More Than 30 Days">More Than 30 Days</option>
                                  </select>
                              </div>
                              <hr>
                              <small>
                              📝 This form section helps users filter and analyze task statuses based on progress and expected completion time.<br><br>
                              It starts with a <b>🔽 "Select Status"</b> dropdown offering:<br>
                              🟡 Tentative &nbsp;&nbsp;|&nbsp;&nbsp; 🟢 Positive<br><br>
                              Based on the selection, users can further refine with:<br>
                              📅 <b>"Propose or Clarify Timeline"</b> — e.g.,  
                              🗓️ Propose Day 6–8, Clarify Day 8–12<br>
                              ⏳ <b>"Closing Timeline"</b> — e.g.,  
                              ⌛ In 8 Days, In 15–30 Days<br><br>
                              This enhances planning accuracy by aligning status, proposal, and closure expectations.
                            </small>

                            </div>
                        </div>
                      </div>
                     

                      <hr>



                      <div class="form-group">
                        <label>Company <span id="companycnt" class="ccount" style="font-size:12px; padding-left:10px"></span></label>
                        <!-- <select id="company" class="form-control select2" name="company[]" multiple>
                          </select> -->
                        <select id="companyListDropdown" class="form-control" name="company[]" multiple>
                          <option value="">Select a Company</option>
                        </select>
                        <small><span id="slct_company_cnt"></span></small>
                      </div>
                      <!-- <div class="form-group">
                        <label>Company <span class="ccount"></span></label>
                        <select id="company" class="form-control" name="company[]">
                        </select>
                        </div> -->
                      <div class="row showoncompanySelection">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Assign Task</label>
                            <select id="atask" class="form-control" name="atask">
                              <option value="">Select Task</option>
                              <?php foreach($action as $a){

                                if(!in_array($a->id,[1,2,3,4,5,22])){
                                  continue;
                                }

                                ?>
                              <option value="<?=$a->id?>"><?=$a->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Target Purpose</label>
                            <select id="ntppose" class="form-control" name="ntppose" required="">
                            </select>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" id="plandate" name="plandate"  value="" min="" required="">
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Time</label>
                            <input type="time" name="tasktimeplan" min="10:00" max="19:00" class="form-control" id="tasktimeplan">
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Target Status</label>
                            <select id="targetstatus" class="form-control" name="targetstatus">
                              <option value="">Select Status</option>
                              <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                              <option value="<?=$status->id?>"><?=$status->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <div class="col-4">
                            <div class="form-group">
                              <label>Target Date</label>
                              <input type="date" class="form-control" id="targetDate" name="targetDate"  value="" min="" required="">
                            </div>
                          </div>
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                          </div>
                        </div>
                      </div>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
      </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {

        $('#propose_or_clarify_filter_select_card').hide();
        $('#closing_timeline_filter_select_card').hide();

            $("#role").change(function(){
        var selectedValue = $(this).val();
        $("#workbyothers").val("");

        console.log(selectedValue);

        $.ajax({
          url: '<?=base_url();?>Menu/getRoleUser',
          type: 'POST', 
          data: {selectedOption: selectedValue},
          success: function(response) {
            $("#user").html(response);
          },
          error: function(xhr, status, error) {
              console.error('Error:', error);
          }
        });
      });

        var currentTime = new Date();
        var currentHours = currentTime.getHours();
        var currentMinutes = currentTime.getMinutes();
        currentHours = (currentHours < 10 ? "0" : "") + currentHours;
        currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
        var currentTimeString = currentHours + ":" + currentMinutes;
        document.getElementById("tasktimeplan").min = currentTimeString;
        $('.showoncompanySelection').hide();
        $('.actionpurpose').hide();
        $('.showcities').hide();
        $('.showpartner').hide();
        $('#company').change(function(){
          if($('#company').val() != ""){
            $('.showoncompanySelection').show();
          }else{
            $('.showoncompanySelection').hide();
          }
        });
        
      $('.select2').select2();
      //select user according to role
  
      var cityCount = 0;
      //select city according to user
      $("#user").change(function() {
        
        var user = $("#user").val(); 
        $("#workbyothers").val("");
        
        if(user != ""){
          $('.showcities').show();
          $('.showpartner').show();
        }else{
          $('.showcities').hide();
          $('.showpartner').hide();
        }
        $('#city').select2('destroy');
        $('#city').select2({
          placeholder: 'Select Cities',
            ajax: {
                url: '<?=base_url();?>Menu/getUserCity',
                type: 'POST',
                dataType: 'json',
                data: function(params) {
                    return {
                        term: params.term,
                        user: user
                    };
                },
                processResults: function(data) {
                  var cities = data.city;
                    cityCount = data.cityCount;
                  var results = $.map(cities, function(item) {
                      return {
                          id: item.city,
                          text: item.city
                      };
                  });
                  return {
                      results: results
                  };
                }
            }
        });
        
      });
      var partnercount=0;
      //Select partner depending on city
      $("#city,#user").on('focus change',function() {
      var user = $("#user").val(); // Use jQuery to get the value
      var cities = $("#city").val(); // Use jQuery to get the value (assuming #city is a multiple-select element)
      var selectedCount = $("#city").val() ? $("#city").val().length : 0;
      $('.citycount').text("Total cities - "+cityCount+' Selected cities - '+selectedCount);
      if(cities == ""){
          $('.showcities').show();
        }else{
          $('.showcities').hide();
        }
      $('#partner').select2({
        placeholder: 'Select Partner',
        ajax: {
          url: '<?=base_url();?>Menu/getUserPartner',
          type: 'POST',
          dataType: 'json',
          data: function(params) {
            return {
              // term: params.term, // Search term entered by the user
              user: user,
              cities: cities
            };
          },
          processResults: function(data) {
              var partners = data.partner;
              partnercount = data.partnerCount;
              var results = $.map(partners, function(item) {
                  return {
                      id: item.partnerType_id,
                      text: item.name
                  };
              });
              return {
                  results: results
              };
            }
        },
      });
      });
      
      var companyCount =0;
      $('#option1,#option2,#days,#previous_status,#current_status,#category,#purpose,#action,#task_action,#user,#city,#partner,#user,#daysbyplandate,#workbyothers,#daysbyplandatewithtask').change(function(){
      var selectedCount = $("#partner").val() ? $("#partner").val().length : 0;
      $('.partnercount').text("Total partner - "+partnercount+' Selected partnercount - '+selectedCount);
      var user =document.getElementById("user").value;
      var cities = $("#city").val();
      var partner =$("#partner").val();
      var previous_status = $("#previous_status").val();
      var option1 =$('#option1:checked').val();
      var option2 =$('#option2:checked').val();
      var days = $("#days").val();
      var current_status = $("#current_status").val();
      var category = $("#category").val();
      var purpose = $("#purpose").val();
      var action = $("#action").val();
      var task_action = $("#task_action").val();
      var daysbyplandate = $("#daysbyplandate").val();
      var workbyothers = $("#workbyothers").val();
      var daysbyplandatewithtask = $("#daysbyplandatewithtask").val();
      
      $('#company').val("");
      if($('#company').val() != ""){
        $('.showoncompanySelection').show();
      }else{
        $('.showoncompanySelection').hide();
      }
      if(option1 == "actionPlanned"){
        $('.actionpurpose').show();
      }else{
        $('.actionpurpose').hide();
      }
      
      $('#user, #city, #partner, #previous_status, #current_status, #days, #option1, #option2, #task_action,#daysbyplandate, #daysbyplandatewithtask, #workbyothers, #category, #action, #purpose').on('change', function () {
      
      var cureentvalue = $(this).val();
      var changedId = $(this).attr('id');
      
      var userid = $('#user').val();
      var cities = $('#city').val();
      var partner = $('#partner').val();
      
      var previous_status = $('#previous_status').val();
      var current_status = $('#current_status').val();
      var daysbyplandate = $('#days').val();
      var option1 = $('#option1').val();
      var option2 = $('#option2').val();
      var task_action = $('#task_action').val();
      var daysbyplandatewithtask = $('#daysbyplandatewithtask').val();
      var workbyothers = $('#workbyothers').val();
      var category = $('#category').val();
      var action = $('#action').val();
      var purpose = $('#purpose').val();
      
      if (userid !== '') {
      $.ajax({
        url: '<?= base_url(); ?>Menu/getcmpWithAssignTask',
        type: 'POST',
        dataType: 'json', 
        data: {
          // term: params.term, // Search term entered by the user
          user: user,
          cities:cities,
          previous_status:previous_status,
          partner:partner,
          current_status:current_status,
          option1:option1,
          option2:option2,
          days:days,
          category:category,
          purpose:purpose,
          task_action:task_action,
          action:action,
          daysbyplandate:daysbyplandate,
          workbyothers:workbyothers,
          daysbyplandatewithtask:daysbyplandatewithtask,
          changedId:changedId
        },
        cache: false,
        success: function (data) {
          if(data !== ''){
            var dropdown = $('#companyListDropdown');
            dropdown.empty();
         
            data.company.forEach(function (company) {
                dropdown.append(
                    $('<option>', {
                        value: company.inid,
                        text: company.compname + ' / ' + company.cid
                    })
                );
            });
            var optionCount = $('#companyListDropdown').find('option').length;
                    optionCount = optionCount-1;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
            // var selectedCount = $("#companyListDropdown").val() ? $("#companyListDropdown").val().length : 0;
          //  $('.ccount').text("Total Company - "+data.companyCount);
          }
        }
      });
      } else {
      alert('* Please select a user');
      }
      });
      
      
      $('#company').select2({
        placeholder: 'Select Company',
        minimumInputLength: 0,
        ajax: {
          url: '<?=base_url();?>Menu/getcmpbylstatus2',
          type: 'POST',
          dataType: 'json',
          // delay: 250,
          data: function(params) {
            // alert(params.term);
            return {
              term: params.term,
              user: user,
              cities:cities,
              previous_status:previous_status,
              partner:partner,
              current_status:current_status,
              option1:option1,
              option2:option2,
              days:days,
              category:category,
              purpose:purpose,
              task_action:task_action,
              action:action,
              daysbyplandate:daysbyplandate,
              workbyothers:workbyothers,
              daysbyplandatewithtask:daysbyplandatewithtask
            };
          },
          processResults: function(data) {
            var dropdown = $('#companyListDropdown');
            dropdown.empty();
            data.company.forEach(function (company) {
                dropdown.append(
                    $('<option>', {
                        value: company.inid,
                        text: company.compname + ' / ' + company.cid
                    })
                );
            });
            var optionCount = $('#companyListDropdown').find('option').length;
                    optionCount = optionCount-1;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
            }
        },
      });
      });
      $("#user,#companyListDropdown").change(function(){
      // var selectedCount = $("#companyListDropdown").val() ? $("#companyListDropdown").val().length : 0;
      // $('.ccount').text("Total Company - "+companyCount+' Selected Company - '+selectedCount);
      });
      
      //select target status according to target task
      $('#atask').on('change', function f() {
      var sid = document.getElementById("current_status").value;
      var aid = document.getElementById("atask").value;
      $.ajax({
        url:'<?=base_url();?>Menu/getpurpose',
        type: "POST",
        data: {
        sid: sid,
        aid: aid
        },
        cache: false,
        success: function a(result){
        $("#ntppose").html(result);
        }
      });
      });
      
      // New Filter Accarding AN-PN, will_give_business, no_status_changes
      
      $('input[name="meetingDone"]').on('change', function() {
      var meetingDone = $('input[name="meetingDone"]:checked').val();
      $("#companyListDropdown").val();
      
        var meetingDone = $(this).val();
        var userid = $('#user').val();
        
        if (userid === null) {
            alert('* Please select a user First');
            $('input[name="meetingDone"][value="yes"]').prop('checked', false);
            $('input[name="meetingDone"][value="no"]').prop('checked', false);
            return false; 
        }
        $.ajax({
            url: '<?= base_url(); ?>Menu/GetCompanyAfterMeetingDone',
            type: 'POST',
            dataType: 'json',
            data: {
              meetingDone: meetingDone,
                userid: userid
            },
            cache: false,
            success: function(data) {
                var dropdown = $('#companyListDropdown');
                dropdown.empty();
                data.company.forEach(function (company) {
                    dropdown.append(
                        $('<option>', {
                            value: company.inid,
                            text: company.compname + ' / ' + company.cid
                        })
                    );
                });
                var optionCount = $('#companyListDropdown').find('option').length;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
            }
        });
      });
      
      
      $('input[name="pst_assign"]').on('change', function() {
      var pst_assign = $('input[name="pst_assign"]:checked').val();
      $("#companyListDropdown").val();
      
        var pst_assign = $(this).val();
        var userid = $('#user').val();
      
        if (userid === null) {
            alert('* Please select a user First');
            $('input[name="pst_assign"][value="yes"]').prop('checked', false);
            $('input[name="pst_assign"][value="no"]').prop('checked', false);
            return false; 
        }
        $.ajax({
            url: '<?= base_url(); ?>Menu/GetCompanyInPSTAssign',
            type: 'POST',
            dataType: 'json',
            data: {
              pst_assign: pst_assign,
                userid: userid
            },
            cache: false,
            success: function(data) {
                var dropdown = $('#companyListDropdown');
                dropdown.empty();
                data.company.forEach(function (company) {
                    dropdown.append(
                        $('<option>', {
                            value: company.inid,
                            text: company.compname + ' / ' + company.cid
                        })
                    );
                });
                var optionCount = $('#companyListDropdown').find('option').length;
                    optionCount = optionCount-1;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
            }
        });
      });
      
      $('input[name="cluster_assign"]').on('change', function() {
      var cluster_assign = $('input[name="cluster_assign"]:checked').val();
      $("#companyListDropdown").val();
      
        var cluster_assign = $(this).val();
        var userid = $('#user').val();
        var pst_assign = $('input[name="pst_assign"]:checked').val();
        if (userid === null) {
            alert('* Please select a user First');
            $('input[name="cluster_assign"][value="yes"]').prop('checked', false);
            $('input[name="cluster_assign"][value="no"]').prop('checked', false);
            return false; 
        }
        $.ajax({
            url: '<?= base_url(); ?>Menu/GetCompanyInClusterAssign',
            type: 'POST',
            dataType: 'json',
            data: {
              cluster_assign: cluster_assign,
              pst_assign:pst_assign,
              userid: userid
            },
            cache: false,
            success: function(data) {
                var dropdown = $('#companyListDropdown');
                dropdown.empty();
                data.company.forEach(function (company) {
                    dropdown.append(
                        $('<option>', {
                            value: company.inid,
                            text: company.compname + ' / ' + company.cid
                        })
                    );
                });
                var optionCount = $('#companyListDropdown').find('option').length;
                    optionCount = optionCount-1;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
            }
        });
      });
      
      
      $('#anpnlastweek').on('change', function () {
        $("#companyListDropdown").val();
      if ($(this).is(':checked')) {
      var anpnlastweek = $(this).val();
      var userid = $('#user').val();
      
      if (userid === null) {
            alert('* Please select a user First');
            $('#anpnlastweek').prop('checked', false);
            return false; 
        }
      
          $.ajax({
              url: '<?= base_url(); ?>Menu/GetCompanyWithANPNAssignTask',
              type: 'POST',
              dataType: 'json',
              data: {
                  anpnlastweek: anpnlastweek,
                  userid: userid
              },
              cache: false,
              success: function (data) {
                  var dropdown = $('#companyListDropdown');
                  dropdown.empty();
                  data.company.forEach(function (company) {
                      dropdown.append(
                          $('<option>', {
                              value: company.inid,
                              text: company.compname + ' / ' + company.cid
                          })
                      );
                  });
                  var optionCount = $('#companyListDropdown').find('option').length;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
              }
          });
      }
      });
      
      
      
      $('#will_give_business').on('change', function () {
      if ($(this).is(':checked')) {
      
      var will_give_business = $(this).val();
      var userid = $('#user').val();
      
      if (userid === null) {
            alert('* Please select a user First');
            $('#will_give_business').prop('checked', false);
            return false; 
        }
      
          $.ajax({
              url: '<?= base_url(); ?>Menu/GetCompanyWillGiveBusiness',
              type: 'POST',
              dataType: 'json',
              data: {
                will_give_business: will_give_business,
                  userid: userid
              },
              cache: false,
              success: function (data) {
                $("#companyListDropdown").val();
                var dropdown = $('#companyListDropdown');
                dropdown.empty();
                data.company.forEach(function (company) {
                    dropdown.append(
                        $('<option>', {
                            value: company.inid,
                            text: company.compname + ' / ' + company.cid
                        })
                    );
                });
                var optionCount = $('#companyListDropdown').find('option').length;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
              }
          });
      }
      });
      $('#no_status_changes').on('change', function () {
      if ($(this).is(':checked')) {
      var no_status_changes = $(this).val();
      var userid = $('#user').val();
      
      if (userid === null) {
            alert('* Please select a user First');
            $('#no_status_changes').prop('checked', false);
            return false; 
        }
      
      
          $.ajax({
              url: '<?= base_url(); ?>Menu/GetCompanyNoStatusChanges',
              type: 'POST',
              dataType: 'json',
              data: {
                no_status_changes: no_status_changes,
                  userid: userid
              },
              cache: false,
              success: function (data) {
                $("#companyListDropdown").val();
                var dropdown = $('#companyListDropdown');
            dropdown.empty();
            data.company.forEach(function (company) {
                dropdown.append(
                    $('<option>', {
                        value: company.inid,
                        text: company.compname + ' / ' + company.cid
                    })
                );
            });
            var optionCount = $('#companyListDropdown').find('option').length;
                  $("#slct_company_cnt").text('Total Company : '+ optionCount);
              }
          });
      }
      });
      $('#review_time_target').on('change', function () {
      if ($(this).is(':checked')) {
      var review_time_target = $(this).val();
      var userid = $('#user').val();
      
      if (userid === null) {
            alert('* Please select a user First');
            $('#review_time_target').prop('checked', false);
            return false; 
        }
      
          $.ajax({
              url: '<?= base_url(); ?>Menu/GetCompanyReviewTimeTargetvsNotAchived',
              type: 'POST',
              dataType: 'json',
              data: {
                review_time_target: review_time_target,
                  userid: userid
              },
              cache: false,
              success: function (data) {
                $("#companyListDropdown").val();
                var dropdown = $('#companyListDropdown');
            dropdown.empty();
            data.company.forEach(function (company) {
                dropdown.append(
                    $('<option>', {
                        value: company.inid,
                        text: company.compname + ' / ' + company.cid
                    })
                );
            });
            var optionCount = $('#companyListDropdown').find('option').length;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
              }
          });
      
      }
      });
      $('#select_week').on('change', function () {
      var select_week = $(this).val();
      if (select_week !== '') {
      var select_week = $(this).val();
      var userid = $('#user').val();
      var meetingDone = $('input[name="meetingDone"]:checked').val();
      const selectedValue = $('input[name="manadatoryfilter"]:checked').val();
      
      var pst_assign = $('input[name="pst_assign"]:checked').val();
      var cluster_assign = $('input[name="cluster_assign"]:checked').val();
      
      if (userid === null) {
            alert('* Please select a user First');
            return false; 
        }
          $.ajax({
              url: '<?= base_url(); ?>Menu/GetCompanyListIsingWeek',
              type: 'POST',
              dataType: 'json',
              data: {
                selectedValue: selectedValue,
                  userid: userid,
                  select_week: select_week,
                  meetingDone: meetingDone,
                  pst_assign:pst_assign,
                  cluster_assign:cluster_assign
              },
              cache: false,
              success: function (data) {
                
                $("#companyListDropdown").val();
                var dropdown = $('#companyListDropdown');
            dropdown.empty();
            data.company.forEach(function (company) {
                dropdown.append(
                    $('<option>', {
                        value: company.inid,
                        text: company.compname + ' / ' + company.cid
                    })
                );
            });
            var optionCount = $('#companyListDropdown').find('option').length;
                    $("#slct_company_cnt").text('Total Company : '+ optionCount);
              }
          });
      }
      });







         $('#new_category_filter_select').on('change', function() {

                  var new_category_filter_select = $(this).val();
                  var selecteduser = $("#user").val();
            
                  if (selecteduser === null || selecteduser == '') {
                        alert('* Please select a user First');
                        $('#new_category_filter_select').prop('selectedIndex', 0);
                        return false; 
                    }

                  if(new_category_filter_select !==''){
                    $.ajax({
                        url:'<?=base_url();?>Menu/GetAllComapanyOnNewCategory',
                        type: "POST",
                        data: {
                          user_id: selecteduser,
                          adate  : '<?= date("Y-m-d"); ?>',
                          new_category_filter   :new_category_filter_select
                        },
                        cache: false,
                        success: function a(result){
                      
                            $("#companyListDropdown").html('');
                            $("#companyListDropdown").html(result);
                            var optionCount = $('#companyListDropdown').find('option').length;
                            if(optionCount > 1){
                              optionCount = optionCount-1;
                            }else{
                              optionCount = 0;
                            }
                            
                            $("#slct_company_cnt").text('Total Company :'+ optionCount);
                        }
                        });
                  }

                          });



 $('#current_quarter_filter_select').on('change', function() {

                          var current_quarter_filter_select = $(this).val();

                           var selecteduser = $("#user").val();
                            if (selecteduser === null) {
                                  alert('* Please select a user First');
                                  $('#new_category_filter_select').prop('selectedIndex', 0);
                                  return false; 
                              }

                          if(current_quarter_filter_select !==''){
                            $.ajax({
                                url:'<?=base_url();?>Menu/GetAllComapanyOnNewCurrentQCategory',
                                type: "POST",
                                data: {
                                  user_id: selecteduser,
                                  adate  : '<?= date("Y-m-d"); ?>',
                                  new_category_filter   :current_quarter_filter_select
                                },
                                cache: false,
                                success: function a(result){
                                      $("#companyListDropdown").html('');
                                      $("#companyListDropdown").html(result);
                                      var optionCount = $('#companyListDropdown').find('option').length;
                                      if(optionCount > 1){
                                        optionCount = optionCount-1;
                                      }else{
                                        optionCount = 0;
                                      }
                                      
                                      $("#slct_company_cnt").text('Total Company :'+ optionCount);
                                   
                                }
                                });
                          }

                          });




$('#closing_timeline_filter_select_status').on('change', function() {

                          var closing_timeline_filter_select_status = $(this).val();

                          var selecteduser = $("#user").val();
                            if (selecteduser === null) {
                                  alert('* Please select a user First');
                                  $('#new_category_filter_select').prop('selectedIndex', 0);
                                  return false; 
                              }


                          if(closing_timeline_filter_select_status == 3){
                             $('#propose_or_clarify_filter_select_card').show();
                             $('#closing_timeline_filter_select_card').hide();

                             $('#propose_or_clarify_filter_select').on('change', function() {

                              var propose_or_clarify_filter_select = $(this).val();

                                if(propose_or_clarify_filter_select !==''){
                                  $.ajax({
                                      url:'<?=base_url();?>Menu/GetProposeOrClarifyFilter',
                                      type: "POST",
                                      data: {
                                        user_id: selecteduser,
                                        adate  : '<?= date("Y-m-d"); ?>',
                                        clarify_filter   :propose_or_clarify_filter_select,
                                        status   :closing_timeline_filter_select_status
                                      },
                                      cache: false,
                                      success: function a(result){
                                          $("#companyListDropdown").html('');
                                          $("#companyListDropdown").html(result);
                                          var optionCount = $('#companyListDropdown').find('option').length;
                                          if(optionCount > 1){
                                            optionCount = optionCount-1;
                                          }else{
                                            optionCount = 0;
                                          }
                                          
                                          $("#slct_company_cnt").text('Total Company :'+ optionCount);
                                      }
                                      });
                                  }
                             });
                             


                          }
                          if(closing_timeline_filter_select_status == 6){
                            $('#propose_or_clarify_filter_select_card').hide();
                            $('#closing_timeline_filter_select_card').show();

                            $('#closing_timeline_filter_select').on('change', function() {

                              var closing_timeline_filter_select = $(this).val();

                                if(closing_timeline_filter_select !==''){
                                  $.ajax({
                                      url:'<?=base_url();?>Menu/GetProposeOrClarifyFilter',
                                      type: "POST",
                                      data: {
                                        user_id: selecteduser,
                                        adate  : '<?= date("Y-m-d"); ?>',
                                        clarify_filter   :closing_timeline_filter_select,
                                        status   :closing_timeline_filter_select_status
                                      },
                                      cache: false,
                                      success: function a(result){
                                            $("#companyListDropdown").html('');
                                            $("#companyListDropdown").html(result);
                                            var optionCount = $('#companyListDropdown').find('option').length;
                                            if(optionCount > 1){
                                              optionCount = optionCount-1;
                                            }else{
                                              optionCount = 0;
                                            }
                                            
                                            $("#slct_company_cnt").text('Total Company :'+ optionCount);
                                      }
                                      });
                                  }
                             });


                          }
                          });






      });
    </script>
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
      <?php require('nav.php');?>
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
                    <form action="<?=base_url();?>Menu/dailyTaskAssign" method="post">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Select Role</label>
                            <select class="custom-select rounded-0" name="role" id="role" >
                              <option>Select Role</option>
                              <?php 
                                foreach($role as $r){
                                  $slcttypeid = $r->id;
                                  if($user['type_id'] == 2){
                                      if($slcttypeid <= $user['type_id']){
                                        continue;
                                      }
                                  }elseif($user['type_id'] == 15){
                                    if ($slcttypeid == 13 || $slcttypeid == 4 || $slcttypeid == 3) { 
                                    }else{
                                      continue;
                                    }
                                  }elseif($user['type_id'] == 4){
                                    if ($slcttypeid == 13 || $slcttypeid == 3) { 
                                    }else{
                                      continue;
                                    }
                                  }elseif($user['type_id'] == 13){
                                    if ($slcttypeid == 3) { 
                                    }else{
                                      continue;
                                    }
                                  }
                                  ?>
                              <option value="<?=$r->id?>"> <?=$r->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Select User</label>
                            <select id="user" class="form-control" name="user">
                            </select>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>City <span class="citycount"  style="font-size:12px; padding-left:10px"></span> <span class="showcities" style="color:red;font-size:12px; padding-left:10px"> (If you did not select city then all cities will be selected for above user)<span></label>
                            <select id="city" class="form-control select2" name="city[]"  multiple="multiple">
                              <option value="">All </option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label>Partner <span class="partnercount"style="font-size:12px; padding-left:10px"></span><span class="showpartner" style="color:red;font-size:12px; padding-left:10px"> (If you did not select partner then all partner will be selected for above user and cities)<span></label>
                        <select id="partner" class="form-control select2" name="partner[]" multiple="multiple">
                          <!-- <option value="">All </option> -->
                        </select>
                      </div>
                      <div class="row">
                        <div class="col-4">
                          <div class="form-group">
                            <label>Earlier Status</label>
                            <select id="previous_status" class="form-control" name="previous_status">
                              <option value="">Select Status</option>
                              <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                              <option value="<?=$status->id?>"><?=$status->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Current Status </label>
                            <select id="current_status" class="form-control" name="current_status" required>
                              <!-- <option value="">All</option> -->
                              <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                              <option value="<?=$status->id?>"><?=$status->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Days</label>
                            <select id="days" class="form-control" name="days">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                            </select>
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
                            <label>Action Planned</label>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="form-group">
                            <input type="radio" id="option2" name="optradio" value="actionNotPlanned">
                            <label>Action Not Planned </label>                          
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
                            <label>Action </label>
                            <select id="action" class="form-control" name="action">
                              <!-- <option value="">All</option> -->
                              <option value="yes">Yes</option>
                              <option value="no">No</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Purpose</label>
                            <select id="purpose" class="form-control" name="purpose">
                              <!-- <option value="">All </option> -->
                              <option value="yes">Yes</option>
                              <option value="no">No </option>
                            </select>
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-group">
                            <label>Days</label>
                            <select id="daysbyplandate" class="form-control" name="daysbyplandate">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-6">
                          <div class="form-group">
                            <label>Task/Action</label>
                            <select id="task_action" class="form-control" name="task_action">
                              <option value="">Select Task</option>
                              <?php $action = $this->Menu_model->get_action();
                                foreach($action as $a){
                                ?>
                              <option value="<?=$a->id?>"><?=$a->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-6">
                          <div class="form-group">
                            <label>Days By Task</label>
                            <select id="daysbyplandatewithtask" class="form-control" name="daysbyplandatewithtask">
                              <option value="">All </option>
                              <option value="8">8 days </option>
                              <option value="15">15 days </option>
                              <option value="more">more than 15 days </option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <!-- <div class="form-group">
                        <input type="radio" id="workbyothers" name="workbyothers" value="workbyothers">
                                                          <label>Work by Others</label>
                        </div> -->
                      <div class="form-group">
                        <label>Work by others</label>
                        <select id="workbyothers" class="form-control" name="workbyothers">
                          <option value="">Select</option>
                          <option value="" id="op1"></option>
                          <option value="admin">Admin</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Category</label>
                        <select id="category" class="form-control" name="category">
                          <option value="">All</option>
                          <option value="topspender">Top Spender</option>
                          <option value="upsell_client">Upsell Client</option>
                          <option value="upsell_client">Upsell Client</option>
                          <option value="focus_funnel">Focus Funnel</option>
                          <option value="keycompany">Key Company</option>
                          <option value="pkclient">P Key Client</option>
                        </select>
                      </div>
                      <div class="card p-2" style="background: beige;">
                        <div class="row">
                          <div class="col-12">
                            <div class="form-group newcentercls">
                              <!-- <input type="radio" id="meetingDone" name="meetingDone" value="after_meeting_Done"> -->
                              <label>&nbsp;&nbsp;Meeting Done : &nbsp;&nbsp;&nbsp;</label>
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
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="form-group newcentercls">
                              <label>&nbsp;&nbsp;PST Assign : &nbsp;&nbsp;&nbsp;</label>
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
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="form-group newcentercls">
                              <label>&nbsp;&nbsp;Cluster Assign : &nbsp;&nbsp;&nbsp;</label>
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
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="form-group newcentercls">
                              <input type="radio" id="anpnlastweek" name="manadatoryfilter" value="anpnlastweek">
                              <label>&nbsp;&nbsp;AN PN Last Week</label>
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="form-group newcentercls">
                              <input type="radio" id="will_give_business" name="manadatoryfilter" value="will_give_business">
                              <label>&nbsp;&nbsp;Will Give Business </label>                          
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="form-group newcentercls">
                              <input type="radio" id="no_status_changes" name="manadatoryfilter" value="No Status Changes">
                              <label>&nbsp;&nbsp;No Status Changes</label>                          
                            </div>
                          </div>
                          <div class="col-3">
                            <div class="form-group newcentercls">
                              <input type="radio" id="review_time_target" name="manadatoryfilter" value="Review Date with target status achived or not">
                              <label>&nbsp;&nbsp;Review Date with target status achived or not</label>                          
                            </div>
                          </div>
                          <div class="col-12 text-center pb-3">
                            <center>
                              <label>Select Week</label>
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
                          </div>
                        </div>
                      </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {
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
      $("#role").change(function(){
        var selectedValue = $(this).val();
        $("#workbyothers").val("");
        if(selectedValue == "4"){
          $('#op1').val('bd').text('BD');
        }
        if(selectedValue == "9"){
          $('#op1').val('pst').text('PST');
        }
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
      });
    </script>
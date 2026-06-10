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
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?=base_url();?>Menu/dailyTaskAssign" method="post">
                            <div class="form-group">
                              <label>Select Role</label>
                              <select class="custom-select rounded-0" name="role" id="role" >
                                  <option>Select Role</option>
                                  <?php foreach($role as $r){
                                    if($r->id == $user['type_id'] && $r->id >= $user['type_id']){
                                      continue;
                                    }
                                    ?>
                                  <option value="<?=$r->id?>"> <?=$r->name?></option>
                                  <?php } ?>
                              </select>
                            </div>
                            <div class="form-group">
                              <label>Select User</label>
                              <select id="user" class="form-control" name="user">
                              </select>
                            </div>
                            <div class="form-group">
                              
                                <label>City <span class="citycount"  style="font-size:12px; padding-left:10px"></span> <span class="showcities" style="color:red;font-size:12px; padding-left:10px"> (If you did not select city then all cities will be selected for above user)<span></label>
                                <select id="city" class="form-control select2" name="city[]"  multiple="multiple">
                                <option value="">All </option>
                                    </select>
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
                                  <label>Action Not Planned </label>                          </div>
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
                            <div class="form-group">
                                <label>Company <span class="ccount" style="font-size:12px; padding-left:10px"></span></label>
                                <select id="company" class="form-control select2" name="company[]" multiple>
                                </select>
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
          
          var user = $("#user").val(); // Use jQuery to get the value
          // Destroy the existing Select2 instance on #city, if any
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
                    // Extract city data and city count from the response
                    var cities = data.city;
                      cityCount = data.cityCount;

                    // Prepare the results array for Select2
                    var results = $.map(cities, function(item) {
                        return {
                            id: item.city,
                            text: item.city
                        };
                    });

                    // Return the results to Select2
                    return {
                        results: results
                    };
                  }
                  // processResults: function(data) {
                    
                  //     return {
                  //         results: $.map(data, function(item) {
                  //             return {
                  //                 id: item.city,
                  //                 text: item.city
                  //             };
                  //         })
                  //     };
                  // }
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
              // alert(params.term);
              return {
                // term: params.term, // Search term entered by the user
                user: user,
                cities: cities // Pass multiple cities as an array or comma-separated string
              };
            },
            processResults: function(data) {
                    // Extract city data and city count from the response
                var partners = data.partner;
                partnercount = data.partnerCount;

                // Prepare the results array for Select2
                var results = $.map(partners, function(item) {
                    return {
                        id: item.partnerType_id,
                        text: item.name
                    };
                });

                // Return the results to Select2
                return {
                    results: results
                };
              }
            // processResults: function(data) {
            //   return {
            //     results: $.map(data, function(item) {
            //       return {
            //         id: item.partnerType_id,
            //         text: item.name
            //       };
            //     })
            //   };
            // }
          },
          // minimumInputLength: 0 // Minimum number of characters required to start searching
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
        
        // alert(workbyothers);
        $('#company').val("");
        if($('#company').val() != ""){
        // var totalCount = $('#company').select2('data').length;
        // $('.ccount').text(totalCount);

          $('.showoncompanySelection').show();
        }else{
          $('.showoncompanySelection').hide();
        }
        //   if(partner == ""){
        //   $('.showpartner').show();
        // }else{
        //   $('.showpartner').hide();
        // }
        if(option1 == "actionPlanned"){
          $('.actionpurpose').show();
        }else{
          $('.actionpurpose').hide();
        }
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
                term: params.term, // Search term entered by the user
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
                    // Extract city data and city count from the response
                var companies = data.company;
                companyCount = data.companyCount;

                // Prepare the results array for Select2
                var results = $.map(companies, function(item) {
                    return {
                        id: item.inid,
                        text: item.compname + ' / ' + item.cid
                    };
                });

                // Return the results to Select2
                return {
                    results: results
                };
              }
            // processResults: function(data) {
            //   // return {
            //   //   results: $.map(data, function(item) {
            //   //     return {
            //   //       id: item.cid,
            //   //       text: item.compname
            //   //     };
            //   //   })
            //   // };
            //   var totalCount = data.length;
            //   $('.ccount').text("(" + totalCount + ")");

            //   // Map the response data to select2 format
            //   var mappedResults = $.map(data, function(item) {
            //       return {
            //           id: item.inid,
            //           text: item.compname + ' / ' + item.cid
            //       };
            //   });
            //   return {
            //     results: mappedResults,
            //     totalCount: totalCount
            //   };

            // }
          },
        });

        // $.ajax({
        //   url: '<?=base_url();?>Menu/getcmpbylstatus2',
        //   type: 'POST', 
        //   data: {
        //     user: user,
        //     cities:cities,
        //     previous_status:previous_status,
        //     partner:partner,
        //     current_status:current_status,
        //     option1:option1,
        //     option2:option2,
        //     days:days,
        //     category:category,
        //     purpose:purpose,
        //     task_action:task_action,
        //     action:action,
        //     daysbyplandate:daysbyplandate,
        //     workbyothers:workbyothers,
        //     daysbyplandatewithtask:daysbyplandatewithtask
        //   },
        //   success: function(response) {
        //       $("#company").html(response);
          
        //   },
        //   error: function(xhr, status, error) {
        //       console.error('Error:', error);
        //   }
        // });
    });


    $("#user,#company").change(function(){
      var selectedCount = $("#company").val() ? $("#company").val().length : 0;
        $('.ccount').text("Total Company - "+companyCount+' Selected Company - '+selectedCount);
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
});
</script>
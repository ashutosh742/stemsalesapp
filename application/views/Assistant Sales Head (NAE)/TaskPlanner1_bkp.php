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
    
    
    h1 {
            font-size: 36px;
            color: #333;
        }

        p {
            font-size: 24px;
            color: #666;
            margin: 10px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
}
  </style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">




<style>


.custom-card {
    margin-top: 50px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
}

.custom-card-header {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px 5px 0 0;
}

.custom-radio-label {
    font-weight: bold;
}
</style>
  
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  

  <!-- Navbar -->
  <?php require('nav.php');?>
  <?php //include 'addpop.php';?>
  <?php //include 'popupbox.php';?>
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
              <h4><?php $uid=$user['user_id']?></h4>
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
         <div class="card col-lg-4 col-sm m-auto p-3">
             <div id="cmpdetail"></div>
             <div id="statusmdetail"></div>
             <div id="locationdetail"></div>
             <div id="categorydetail"></div>
             <div id="notask15ddetail"></div>
             <div id="tobescheduled"></div>
             <div id="samesfld"></div>
             <div id="preplanned"></div>
             <div id="reviewtarget"></div>
         </div>
         
        <div class="justify-content-center col-lg-4 col-sm m-auto p-3">
             <div class="card custom-card">
                 <?=form_open('Menu/TaskPlanner/'.$adate)?><input type="date" class="form-control" name="adate" value="<?=$adate?>" value="<?=$adate?>" required="" id="plandate"><input type="submit" value="Set Date"></form>
               <hr>
                    <div class="card-header custom-card-header">
                        <h5>Task Planner</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Compnay Name" onchange="handleRadioChange(this)">Company Name
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Status" onchange="handleRadioChange(this)">Status
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Location" onchange="handleRadioChange(this)">Location
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Category" onchange="handleRadioChange(this)">Category
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="No Task Between Last 15 Days" onchange="handleRadioChange(this)">No Task Between Last 15 Days
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="In To be Scheduled" onchange="handleRadioChange(this)">In To be Scheduled
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Same Status from Limit Date" onchange="handleRadioChange(this)">Same Status from Limit Date
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Pre planned for that day" onchange="handleRadioChange(this)">Pre planned for that day
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Review Target" onchange="handleRadioChange(this)">Review Target
                            </label>
                        </div>
                        <!-- <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="PST Assigned" onchange="handleRadioChange(this)">PST Assigned
                            </label>
                        </div> -->
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="PST Assigned Not Worked" onchange="handleRadioChange(this)">PST Assigned Not Worked
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Annual Review Target Date Company" onchange="handleRadioChange(this)">Annual Review Target Date Company
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="Reporting Manager Target Date Company" onchange="handleRadioChange(this)">Reporting Manager Target Date Company
                            </label>
                        </div>
                        <!-- <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="optradio" value="PST Not Assigned" onchange="handleRadioChange(this)">PST Not Assigned
                            </label>
                        </div> -->
                        <hr>
                        <div class="card-header">
                            <b>Let's Start Creating Task for <span id="tasktype"></span></b>
                        </div>
                    </div>
                        
                        
                        <div class="card mt-3 p-3">
                            
                            <div class="col-md-12 col-sm" id="box0">
                                <input list="brow" id="bycmp" class="form-control">
                                <datalist id="brow">
                                  <?php $fannal = $this->Menu_model->get_fannal($uid);foreach($fannal as $fannal){?>
                                    <option value="<?=$fannal->inid?>"><?=$fannal->compname?></option>
                                    <?php } ?>
                                </datalist>
                            </div>
                            
                            <div class="col-md-12 col-sm" id="box1">
                                <select class="form-control" id="statusid">
                                    <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                                    <option value="<?=$status->id?>"><?=$status->name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12 col-sm" id="box2">
                                <select class="form-control" id="bylocation">
                                    <?php $location = $this->Menu_model->get_bdlocation($uid);foreach($location as $location){?>
                                    <option><?=$location->city?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-12 col-sm" id="box3">
                                <select class="form-control" id="category">
                                    <option value="topspender">Top Spender</option>
                                    <option value="upsell_client">Upsell Client</option>
                                    <option value="focus_funnel">Focus Funnel</option>
                                    <option value="keycompany">Key Company</option>
                                    <option value="pkclient">P Key Client</option>
                                </select>
                            </div>
                            
                            
                            <div class="col-md-12 col-sm" id="box4">
                                <select class="form-control" id="statusid4">
                                    <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                                    <option value="<?=$status->id?>"><?=$status->name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-12 col-sm" id="box6">
                                <select class="form-control" id="statusid6">
                                    <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                                    <option value="<?=$status->id?>"><?=$status->name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            
                            <div class="col-md-12 col-sm" id="box5">
                                <select class="form-control" id="statusid5">
                                    <?php $mstatus = $this->Menu_model->get_status();foreach($mstatus as $status){?>
                                    <option value="<?=$status->id?>"><?=$status->name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            
                            
                            <div class="col-md-12 col-sm" id="ScheduledBox">
                                   <?=form_open('Menu/dateplan/'.$adate.'/'.$uid)?>
                                   <lable>Select Task</lable>
                                   <select class="form-control" name="taskid">
                                       <?php $totalt=$this->Menu_model->get_totalt($uid,$adate); $ai=0;foreach($totalt as $tt){if($tt->plan==0){if($tt->autotask==0){?>
                                       <option value="<?=$tt->id?>"><?=$tt->aname?> <?=$tt->compname?> <?=$tt->csname?></option>
                                       <?php $ai++;}}} ?>
                                   </select>
                                   <lable>Select Time</lable>
                                   <!-- <input type="time" name="tasktimeplan" min="10:00" max="19:00" class="form-control" id="tasktimeplan"> -->
                                   <input type="time" name="tasktimeplan" min="10:00" max="19:00" class="form-control" id="tasktimeplan">
                                   <button type="submit"  class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                                   </form>
                            </div>
                            
                            
                        </div>
                        
                        <div class="card">
                            <div class="card-body" id="mainbox">
                                <form method="post" action="<?=base_url();?>Menu/addplantask">
                                    <div class="was-validated">
                                        <input type="hidden" id="userid" value="<?=$uid?>" name="bdid" required=""> 
                                        <div class="col-md-12 col-sm mt-3">
                                           <input type="hidden" class="form-control" id="ttype" name="ttype" required="">
                                           <input type="text" class="form-control" id="tptime" name="tptime" required="">
                                           <input type="hidden" class="form-control" name="pdate" value="<?=$adate?>" required="">
                                           <input type="hidden" name="ntuid" value="<?=$uid?>" required="">
                                           <input type="time" id="meeting-time" name="ptime" min="10:00" max="19:00" class="form-control" required="">
                                          
                                        <label>Last Status</label>
                                        <input type="text" class="form-control" name="last_status" id="lastStatus" value="" readonly>
                            
                                           <lable>Select Company</lable>
                                           <select class="form-control" id="inid" name="inid" required=""></select>
                                           
                                           <lable>Select Action</lable>
                                           <select id="ntaction" name="ntaction" class="form-control" required="">
                                               <option value="">Select Action</option>
                                               <?php $action = $this->Menu_model->get_action();
                                               foreach($action as $a){if($a->id!=4 && $a->id!=6 && $a->id!=8 && $a->id!=9 && $a->id!=11 && $a->id!=12){ 
                                               ?>
                                                   <option value="<?=$a->id?>"><?=$a->name?></option>
                                               <?php }} ?>
                                           </select>
                                           <div id="ntstatus" required=""></div>
                                           <select id="ntppose" class="form-control" name="ntppose" required=""></select>
                                        <button class="btn btn-primary m-3" type="submit"id="planbtn1">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        
                      </div> 
                    </div>
            
            <!--######-->
            
            
         </div>
         <div class="card col-lg-4 col-sm m-auto p-3" id="content">
             
<p id="demo" class="text-right card p-2">Time Spent in Planning: 00:00:00</p>
<center><b><i>Total Plan Time : <?=$tptime?></i></b>
<p class="m-auto" id="chart_div"></p><hr></center>
<div class="row">
    <div class="col-lg-6 col-sm" id="piechart1"></div>
    <div class="col-lg-6 col-sm" id="piechart2"></div>
</div>




<script>
<?php $totaltaktimep = $this->Menu_model->get_totaltaktimep($uid,$adate); $ttime = $totaltaktimep[0]->ttime; $ttime = $ttime/60;?>

var pageLoadTime = new Date().getTime() - 0;
var x = setInterval(function() {
  var now = new Date().getTime();
  var timeSpent = now - pageLoadTime;
  var hours = Math.floor((timeSpent / 1000) / 3600);
  var minutes = Math.floor(((timeSpent / 1000) % 3600) / 60);
  var seconds = Math.floor((timeSpent / 1000) % 60);
  var formattedTimeSpent = 
    (hours < 10 ? "0" : "") + hours + ":" +
    (minutes < 10 ? "0" : "") + minutes + ":" +
    (seconds < 10 ? "0" : "") + seconds;
    document.getElementById("demo").innerHTML = "Time Spent in Planning: " + formattedTimeSpent;
    document.getElementById("tptime").value=formattedTimeSpent;
}, 1000);
</script>

        
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {'packages':['gauge']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        ['Planning', <?=$ttime?>]
      ]);
      var options = {
          redFrom: 0,
          redTo: 3,
          yellowFrom: 3,
          yellowTo: 6,
          greenFrom: 6,
          greenTo: 8,
          minorTicks: 4,
          max: 8
      };
      var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart2);
      function drawChart2() {
        var data = google.visualization.arrayToDataTable([
          ['Status', 'No of Task'],
            <?php $action = $this->Menu_model->get_tttbytimedaction($uid,$adate);
             foreach($action as $ac){?>
             ["<?=$ac->acname?> (<?=$ac->cont?>)", <?=$ac->cont?>],
    	    <?php } ?>
        ]);

        var options = {
          is3D: false,
          
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart1'));
        chart.draw(data, options);
      }
      
      
      
      
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart4);
      function drawChart4() {
        var data = google.visualization.arrayToDataTable([
          ['Status', 'No of Task'],
            <?php $status = $this->Menu_model->get_tttbytimedstatus($uid,$adate);
             foreach($status as $st){?>
             ["<?=$st->stname?> (<?=$st->cont?>)", <?=$st->cont?>],
    	    <?php } ?>
        ]);

        var options = {
          is3D: false,
          
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
        chart.draw(data, options);
      }
      
      
      
  </script>



  
  
  <hr>
    
    <div>
        
        
        
        <div id="accordion">
              <div class="card">
                <div class="card-header" id="headingOne">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-controls="collapseOne">
                      <h6>Task Planned for <?=$adate?></h6>
                    </button>
                </div>
            
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                  <div class="card-body">
                      
                      <?php $tted = $this->Menu_model->get_tttbytimedaction($uid,$adate); foreach($tted as $ted){?>
                            <span class="badge"  style="background-color:<?=$ted->aclr?>"><?=$ted->acname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                        <?php } ?>
                        <hr>
                        <?php $tted = $this->Menu_model->get_tttbytimedstatus($uid,$adate); foreach($tted as $ted){?>
                            <span class="badge" style="background-color:<?=$ted->sclr?>"><?=$ted->stname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                        <?php } ?>
                        
                        <hr>
                        <h5></h5>
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          
                          ?>
                            <div class="card border border-info">
                                <div class="card-header">
                                    <b><?=date("h:i A", strtotime($tl->time1));?> to <?=date("h:i A", strtotime($tl->time2));?></b>
                                    </br>
                                    <?php  $ted = $this->Menu_model->get_ttbytimedaction($uid,$adate,$t1,$t2); foreach($ted as $ted){
                                      ?>
                                      <?php if($ted){?>
                                      <input type="hidden" id="timeslot-alloted_s" name="timeslot-alloted_s" value="<?=$tl->time1?>">
                                      <input type="hidden" id="timeslot-alloted_e" name="timeslot-alloted_e" value="<?=$tl->time2?>">
                                      <?php } ?>
                                        <span class="badge" style="background-color:<?=$ted->aclr?>"><?=$ted->acname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                    <?php } ?>
                                    <hr>
                                    <?php $ted = $this->Menu_model->get_ttbytimedstatus($uid,$adate,$t1,$t2); foreach($ted as $ted){?>
                                        <span class="badge" style="background-color:<?=$ted->sclr?>"><?=$ted->stname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                    <?php } ?>
                                </div>
                                <?php $totaltaktimep = $this->Menu_model->get_totaltaktimepbyh($uid,$adate,$t1,$t2); 
                                $ttime = $totaltaktimep[0]->ttime;
                                if($ttime>'120'){$bcolor="bg-success"; $msg="Great! You have been scheduled for full-time utilization.";}
                                elseif($ttime=='0'){$bcolor="bg-danger";$msg="Caution! Make sure to plan for this period.";}
                                else{$bcolor="bg-warning";$msg="Nice job! Consider planning additional tasks.";}
                                ?>
                                <div class="card-footer <?=$bcolor?>"><b><?=$msg?></b></div>
                            </div>
                        <?php } ?>
                                    
                    
                  </div>
                </div>
              </div>
              
              <button id="printButton">Print</button>
              
              </div>
            </div>
        
    </div>  
    
    <section class="content">
      <div class="container-fluid">
          <div class="p-3" id="logs"></div>
          </div>
    </section>
    
        
         </div>
        </div>
        </div>
    </section> 
    
    
    
    </div>
        </div>
        
        </div>
        </div>
        
        
        
        
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>  
<script type='text/javascript'>

document.getElementById('printButton').addEventListener('click', function() {
    var contentToPrint = document.getElementById('content').outerHTML;
    var printWindow = window.open('', '', 'width=600,height=600');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(contentToPrint);
    printWindow.document.write('</body></html>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">');
    
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
});

  var currentTime = new Date();
  var currentHours = currentTime.getHours();
  var currentMinutes = currentTime.getMinutes();
  currentHours = (currentHours < 10 ? "0" : "") + currentHours;
  currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;

  var currentTimeString = currentHours + ":" + currentMinutes;
  
  var year = currentTime.getFullYear();
  var month = ("0" + (currentTime.getMonth() + 1)).slice(-2); // Month is zero-based, so we add 1 and pad with leading zero if needed
  var day = ("0" + currentTime.getDate()).slice(-2); // Pad with leading zero if needed
  // Construct the desired date format (YYYY-MM-DD)
  var formattedDate = year + "-" + month + "-" + day;
  var plandate = document.getElementById("plandate").value;
  if(plandate == formattedDate){
    document.getElementById("meeting-time").min = currentTimeString;
    document.getElementById("tasktimeplan").min = currentTimeString;
  }
//   document.getElementById("meeting-time").min = currentTimeString;
//   document.getElementById("tasktimeplan").min = currentTimeString;
$(document).ready(function() {
  $('#meeting-time').on('change', function() {
    var enteredTime = $(this).val();
    var time = new Date('1970-01-01T' + enteredTime);

    // Get hours, minutes, and seconds
    var hours = ('0' + time.getHours()).slice(-2); // Ensure leading zero
    var minutes = ('0' + time.getMinutes()).slice(-2); // Ensure leading zero
    var seconds = ('0' + time.getSeconds()).slice(-2); 
    var formattedTime = hours + ':' + minutes + ':' + seconds;
    var starttime = $('#timeslot-alloted_s').val();
    var endtime = $('#timeslot-alloted_e').val();
    var startMs = convertToMilliseconds($('#timeslot-alloted_s').val());
    var endMs = convertToMilliseconds($('#timeslot-alloted_e').val());
    var enteredMs = convertToMilliseconds(enteredTime);
    // Check if entered time is between start and end time frames
    if(plandate == formattedDate){
        if (enteredMs >= startMs && enteredMs <= endMs) {
          alert("You already have plan for this time slot");
          $('#planbtn1').css('display', 'none');
    
        } else {
          $('#planbtn1').css('display', '');
        }
    }
  });
  function convertToMilliseconds(timeStr) {
    var parts = timeStr.split(':');
    var hours = parseInt(parts[0], 10);
    var minutes = parseInt(parts[1], 10);
    var seconds = parseInt(parts[2], 10) || 0; // Default to 0 seconds if not provided
    return hours * 3600000 + minutes * 60000 + seconds * 1000;
  }
});


$("#cmpdetail,#statusmdetail,#locationdetail,#categorydetail,#notask15ddetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
  var radioButtons = document.querySelectorAll('input[name="optradio"]');
  radioButtons.forEach(function(radio) {
  radio.addEventListener('change', function() {
      var val = radio.value;
      if(val=='Compnay Name'){
          $("#statusmdetail,#locationdetail,#categorydetail,#notask15ddetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
          $("#cmpdetail").show();
          google.charts.load("current", {packages:["corechart"]});
          google.charts.setOnLoadCallback(drawChart10);
          function drawChart10() {
            var data = google.visualization.arrayToDataTable([
              ['Status', 'No of Compnay'],
            <?php $partner = $this->Menu_model->get_fannalpartner($uid);
             foreach($partner as $pa){?>
             ["<?=$pa->pname?> (<?=$pa->cont?>)", <?=$pa->cont?>],
            <?php } ?>
            
            ]);
            var options = {
              title: 'Compnay Name',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('cmpdetail'));
            chart.draw(data, options);
          }
      }
      
      
      if(val=='Status'){
          $("#locationdetail,#categorydetail,#cmpdetail,#notask15ddetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
          $("#statusmdetail").show();
          google.charts.load("current", {packages:["corechart"]});
          google.charts.setOnLoadCallback(drawChart10);
          function drawChart10() {
            var data = google.visualization.arrayToDataTable([
              ['Status', 'No of Compnay'],
            <?php $partner = $this->Menu_model->get_fannalpartner($uid);
             foreach($partner as $pa){?>
             ["<?=$pa->pname?> (<?=$pa->cont?>)", <?=$pa->cont?>],
            <?php } ?>
            
            ]);
            var options = {
              title: 'Status Type wise funnel',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('statusmdetail'));
            chart.draw(data, options);
          }
      }
      
      
      if(val=='Location'){
          $("#statusmdetail,#categorydetail,#cmpdetail,#notask15ddetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
          $("#locationdetail").show();
          google.charts.setOnLoadCallback(drawChart13);
          function drawChart13() {
            var data = google.visualization.arrayToDataTable([
              ['City', 'No of Compnay'],
                <?php $city = $this->Menu_model->get_fannalcitywise($uid);
                 foreach($city as $ci){?>
                 ["<?=$ci->city?> (<?=$ci->cont?>)", <?=$ci->cont?>],
        	    <?php } ?>
            ]);
    
            var options = {
              title: 'City wise funnel',
              is3D: true,
            };
    
            var chart = new google.visualization.PieChart(document.getElementById('locationdetail'));
            chart.draw(data, options);
          }
      }
      
      
      
      
      if(val=='Category'){
          $("#statusmdetail,#locationdetail,#cmpdetail,#notask15ddetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
          $("#categorydetail").show();
          google.charts.setOnLoadCallback(drawChart11);
          function drawChart11() {
            var data = google.visualization.arrayToDataTable([
              ['Category', 'No of Compnay'],
              <?php $cat = $this->Menu_model->get_fannalcat($uid);?>
             ["No Category (<?=$cat[0]->nocat?>)", <?=$cat[0]->nocat?>],
             ["Top Spender (<?=$cat[0]->topspender?>)", <?=$cat[0]->topspender?>],
             ["Focus Funnel (<?=$cat[0]->focus_funnel?>)", <?=$cat[0]->focus_funnel?>],
             ["Upsell Client (<?=$cat[0]->upsell_client?>)", <?=$cat[0]->upsell_client?>],
             ["Key Client (<?=$cat[0]->keycompany?>)", <?=$cat[0]->keycompany?>],
             ["Positive Key Client (<?=$cat[0]->pkclient?>)", <?=$cat[0]->pkclient?>],
             ["Priority Client (<?=$cat[0]->priorityc?>)", <?=$cat[0]->priorityc?>],
            ]);
            var options = {
              title: 'Category wise funnel',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('categorydetail'));
            chart.draw(data, options);
          } 
      }
      
      
      if(val=='No Task Between Last 15 Days'){
          $("#statusmdetail,#locationdetail,#cmpdetail,#categorydetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
          $("#notask15ddetail").show();
          google.charts.setOnLoadCallback(drawChart12);
          function drawChart12() {
            var data = google.visualization.arrayToDataTable([
              ['Category', 'No of Compnay'],
              <?php $cat = $this->Menu_model->get_fannalcat($uid);?>
             ["No Category (<?=$cat[0]->nocat?>)", <?=$cat[0]->nocat?>],
             ["Top Spender (<?=$cat[0]->topspender?>)", <?=$cat[0]->topspender?>],
             ["Focus Funnel (<?=$cat[0]->focus_funnel?>)", <?=$cat[0]->focus_funnel?>],
             ["Upsell Client (<?=$cat[0]->upsell_client?>)", <?=$cat[0]->upsell_client?>],
             ["Key Client (<?=$cat[0]->keycompany?>)", <?=$cat[0]->keycompany?>],
             ["Positive Key Client (<?=$cat[0]->pkclient?>)", <?=$cat[0]->pkclient?>],
             ["Priority Client (<?=$cat[0]->priorityc?>)", <?=$cat[0]->priorityc?>],
            ]);
            var options = {
              title: 'No Task B/W Last 15 Days',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('notask15ddetail'));
            chart.draw(data, options);
          } 
      }
      
      
      
      if(val=='In To be Scheduled'){
          $("#statusmdetail,#locationdetail,#cmpdetail,#categorydetail,#notask15ddetail,#samesfld,#preplanned,#reviewtarget").hide();
          $("#tobescheduled").show();
          google.charts.setOnLoadCallback(drawChart12);
          function drawChart12() {
            var data = google.visualization.arrayToDataTable([
              ['Category', 'No of Compnay'],
              <?php $cat = $this->Menu_model->get_fannalcat($uid);?>
             ["No Category (<?=$cat[0]->nocat?>)", <?=$cat[0]->nocat?>],
             ["Top Spender (<?=$cat[0]->topspender?>)", <?=$cat[0]->topspender?>],
             ["Focus Funnel (<?=$cat[0]->focus_funnel?>)", <?=$cat[0]->focus_funnel?>],
             ["Upsell Client (<?=$cat[0]->upsell_client?>)", <?=$cat[0]->upsell_client?>],
             ["Key Client (<?=$cat[0]->keycompany?>)", <?=$cat[0]->keycompany?>],
             ["Positive Key Client (<?=$cat[0]->pkclient?>)", <?=$cat[0]->pkclient?>],
             ["Priority Client (<?=$cat[0]->priorityc?>)", <?=$cat[0]->priorityc?>],
            ]);
            var options = {
              title: 'Task is in to be scheduled',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('tobescheduled'));
            chart.draw(data, options);
          } 
      }
      
      
      
      if(val=='Same Status from Limit Date'){
          $("#statusmdetail,#locationdetail,#cmpdetail,#categorydetail,#tobescheduled,#notask15ddetail,#preplanned,#reviewtarget").hide();
          $("#samesfld").show();
          google.charts.setOnLoadCallback(drawChart12);
          function drawChart12() {
            var data = google.visualization.arrayToDataTable([
              ['Category', 'No of Compnay'],
              <?php $cat = $this->Menu_model->get_fannalcat($uid);?>
             ["No Category (<?=$cat[0]->nocat?>)", <?=$cat[0]->nocat?>],
             ["Top Spender (<?=$cat[0]->topspender?>)", <?=$cat[0]->topspender?>],
             ["Focus Funnel (<?=$cat[0]->focus_funnel?>)", <?=$cat[0]->focus_funnel?>],
             ["Upsell Client (<?=$cat[0]->upsell_client?>)", <?=$cat[0]->upsell_client?>],
             ["Key Client (<?=$cat[0]->keycompany?>)", <?=$cat[0]->keycompany?>],
             ["Positive Key Client (<?=$cat[0]->pkclient?>)", <?=$cat[0]->pkclient?>],
             ["Priority Client (<?=$cat[0]->priorityc?>)", <?=$cat[0]->priorityc?>],
            ]);
            var options = {
              title: 'Same Status from Till Day',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('samesfld'));
            chart.draw(data, options);
          } 
      }
      
      
      if(val=='Pre planned for that day'){
          $("#statusmdetail,#locationdetail,#cmpdetail,#categorydetail,#tobescheduled,#notask15ddetail,#samesfld,#reviewtarget").hide();
          $("#preplanned").show();
          google.charts.setOnLoadCallback(drawChart12);
          function drawChart12() {
            var data = google.visualization.arrayToDataTable([
              ['Status', 'No of Compnay'],
            <?php $partner = $this->Menu_model->get_fannalpartner($uid);
             foreach($partner as $pa){?>
             ["<?=$pa->pname?> (<?=$pa->cont?>)", <?=$pa->cont?>],
            <?php } ?>
            
            ]);
            var options = {
              title: 'Pre planned for that day',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('preplanned'));
            chart.draw(data, options);
          } 
      }
      
      if(val=='Review Target'){
          $("#statusmdetail,#locationdetail,#cmpdetail,#categorydetail,#tobescheduled,#notask15ddetail,#preplanned,#samesfld").hide();
          $("#reviewtarget").show();
          google.charts.setOnLoadCallback(drawChart12);
          function drawChart12() {
            var data = google.visualization.arrayToDataTable([
              ['Status', 'No of Compnay'],
            <?php $partner = $this->Menu_model->get_fannalpartner($uid);
             foreach($partner as $pa){?>
             ["<?=$pa->pname?> (<?=$pa->cont?>)", <?=$pa->cont?>],
            <?php } ?>
            
            ]);
            var options = {
              title: 'Review Target',
              is3D: true,
            };
            var chart = new google.visualization.PieChart(document.getElementById('reviewtarget'));
            chart.draw(data, options);
          } 
      }
      
      
      
      
      
      
      
      
      
    });
});
</script>








<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
$("#mainbox").hide();$("#ScheduledBox").hide();
$("#box0").hide();$("#box1").hide();$("#box2").hide();$("#box3").hide();$("#box4").hide();$("#box5").hide();
var radioButtons = document.querySelectorAll('input[name="optradio"]');
radioButtons.forEach(function(radio) {
  radio.addEventListener('change', function() {
     var val = radio.value;
     if(val=='Compnay Name'){
         $("#box1,#box2,#box3,#box6,#mainbox,#ScheduledBox").hide();
         $("#box0").show();
         $("#tasktype").html('Company Name Wise');
     }
     
     if(val=='Status'){
         $("#box1").show();
         $("#box0,#box2,#box3,#box4,#box5,#box6,#mainbox,#ScheduledBox").hide();
         $("#tasktype").html('Status Wise');
     }
     
     
     if(val=='PST Assigned'){
         $("#box0,#box1,#box2,#box3,#box5,#box6,#ScheduledBox").hide();
         $("#box4").show();
         $("#tasktype").html('PST Assigned');
     }
     
     if(val=='PST Not Assigned'){
         $("#box0,#box1,#box2,#box3,#box5,#ScheduledBox").hide();
         $("#box6").show();
         $("#tasktype").html('PST Assigned');
     }
     
     if(val=='Location'){
         $("#box0,#box1,#box3,#box4,#box5,#box6,#mainbox").hide();
         $("#box2").show();
         $("#tasktype").html('Location Wise');
     }
     
     if(val=='Category'){
         $("#box0,#box1,#box2,#box4,#box5,#box6,#mainbox,#ScheduledBox").hide();
         $("#box3").show();
         $("#tasktype").html('Category Wise');
     }
     
     if(val=='In To be Scheduled'){
         $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#mainbox").hide();
         $("#ScheduledBox").show();
         $("#tasktype").html('In To be Scheduled');
     }
     
     if(val=='PST Assigned Not Worked'){
         $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
         $("#mainbox").show();
         document.getElementById("ttype").value=11;
         $("#tasktype").html('PST Assigned Not Worked from last 8 days Wise');
         var uid = document.getElementById("userid").value;
            $.ajax({
                url:'<?=base_url();?>Menu/getcmpbynwpsta',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                $("#inid").html(result);
                }
            });
     }
     
     if(val=='Annual Review Target Date Company'){
        $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
        $("#mainbox").show();
        document.getElementById("ttype").value=11;
        $("#tasktype").html('Annual Review Target Date Company');
        var uid = document.getElementById("userid").value;
        var plandate = document.getElementById("plandate").value;
          
        $.ajax({
            url:'<?=base_url();?>Menu/getcmpbyannualreview',
            type: "POST",
            data: {
            uid: uid,
            plandate: plandate
            },
            cache: false,
            success: function a(result){
            $("#inid").html(result);
            }
        });
      }
      
    if(val=='Reporting Manager Target Date Company'){
        $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
        $("#mainbox").show();
        document.getElementById("ttype").value=11;
        $("#tasktype").html('Reporting Manager Target Date Company');
        var uid = document.getElementById("userid").value;
        var plandate = document.getElementById("plandate").value;
          
        $.ajax({
            url:'<?=base_url();?>Menu/getcmpbyRMTaskAssign',
            type: "POST",
            data: {
            uid: uid,
            plandate: plandate
            },
            cache: false,
            success: function a(result){
            $("#inid").html(result);
            }
        });
    }
      
     if(val=='No Task Between Last 15 Days'){
         $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
         $("#mainbox").show();
          // $("#box1").show();
         document.getElementById("ttype").value=5;
         $("#tasktype").html('No Task Between Last 15 Days Wise');
         var uid = document.getElementById("userid").value;
         var sid = 0;
         
            $.ajax({
                url:'<?=base_url();?>Menu/getcmpbynwbwd',
                type: "POST",
                data: {
                uid: uid,
                sid:sid,
                radioval:val
                },
                cache: false,
                success: function a(result){
                $("#inid").html(result);
                }
            });
     }
     
     if(val=='Same Status from Limit Date'){
         $("#box0,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();//
         $("#mainbox").show();
         $('#box1').show();
         document.getElementById("ttype").value=7;
         $("#tasktype").html('Same Status from Limit Date');
         var uid = document.getElementById("userid").value;
         var sid = document.getElementById("statusid").value;
            $.ajax({
                url:'<?=base_url();?>Menu/getcmpbynwbwd',
                type: "POST",
                data: {
                uid: uid,
                sid: sid
                },
                cache: false,
                success: function a(result){
                  console.log(result);
                $("#inid").html(result);
                }
            });
     }
     
     
     
     
     if(val=='Pre planned for that day'){
         $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
         $("#mainbox").show();
         document.getElementById("ttype").value=8;
         $("#tasktype").html('Pre planned for that day');
         var uid = document.getElementById("userid").value;
         var plandate = document.getElementById("plandate").value;
         var sid =0;
            $.ajax({
                url:'<?=base_url();?>Menu/getcmpbypreplandate',
                type: "POST",
                data: {
                uid: uid,
                sid:sid,
                plandate:plandate
                },
                cache: false,
                success: function a(result){
                $("#inid").html(result);
                }
            });
     }
     
     if(val=='Review Target'){
      $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
      $("#mainbox").show();
      document.getElementById("ttype").value=9;
      $("#tasktype").html('Review Target');
      var uid = document.getElementById("userid").value;
      var plandate = document.getElementById("plandate").value;
      $.ajax({
        url:'<?=base_url();?>Menu/getcmpbyreview',
        type: "POST",
        data: {
        uid: uid,
        plandate:plandate
        },
        cache: false,
        success: function a(result){
        $("#inid").html(result);
        }
      });
    }

    if(val=='PST Assigned'){
      $("#box0,#box1,#box2,#box3,#box4,#box5,#box6,#ScheduledBox").hide();
      $("#mainbox").show();
      document.getElementById("ttype").value=9;
      $("#tasktype").html('PST Assigned');
      var uid = document.getElementById("userid").value;
      $.ajax({
        url:'<?=base_url();?>Menu/getcmpbypstassign',
        type: "POST",
        data: {
        uid: uid,
        plandate:plandate
        },
        cache: false,
        success: function a(result){
        $("#inid").html(result);
        }
      });
    }
     
     
     
     
  });
});


$('#inid').on('change', function b() {
   var inid = document.getElementById("inid").value;
   $.ajax({
    url:'<?=base_url();?>Menu/getinitlogs',
    type: "POST",
    data: {
    inid: inid
    },
    cache: false,
    success: function a(result){
    $("#logs").html(result);
    }
    });
});

$('#inid').on('change', function () {
   var inid = document.getElementById("inid").value;
   $.ajax({
    url:'<?=base_url();?>Menu/getstatusbycompany',
    type: "POST",
    data: {
    inid: inid
    },
    cache: false,
    success: function a(response){
        // alert( response);
        var responseData = response;
        // console.log(responseData);
                // Set the value of the input field
                $('#lastStatus').val(responseData);
    }
    });
});



$('#ntaction').on('change', function b() {
var inid = document.getElementById("inid").value;
$.ajax({
url:'<?=base_url();?>Menu/getinidtostatus',
type: "POST",
data: {
inid: inid
},
cache: false,
success: function a(result){
$("#ntstatus").html(result);
}
});
});
    
$('#ntaction').on('change', function f() {
    var inid = document.getElementById("inid").value;
    var aid = document.getElementById("ntaction").value;
    $.ajax({
    url:'<?=base_url();?>Menu/getpurposebyinid',
    type: "POST",
    data: {
    inid: inid,
    aid: aid
    },
    cache: false,
    success: function a(result){
    $("#ntppose").html(result);
    }
    });
});



$('#bycmp').on('change', function b() {
  $("#mainbox").show();
  document.getElementById("ttype").value=1;
  var bycmp = document.getElementById("bycmp").value;
  $.ajax({
    url:'<?=base_url();?>Menu/getcmpbycmp',
    type: "POST",
    data: {
    bycmp: bycmp
    },
    cache: false,
    success: function a(result){
    $("#inid").html(result);
    }
  });
});






$('#statusid4').on('change', function b() {
$("#mainbox").show();
document.getElementById("ttype").value=10;
var sid = document.getElementById("statusid4").value;
var uid = document.getElementById("userid").value;
$.ajax({
url:'<?=base_url();?>Menu/getstatuscmp4',
type: "POST",
data: {
sid: sid,
uid: uid
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
});


$('#statusid6').on('change', function b() {
  $("#mainbox").show();
  document.getElementById("ttype").value=12;
  var sid = document.getElementById("statusid6").value;
  var uid = document.getElementById("userid").value;
  $.ajax({
    url:'<?=base_url();?>Menu/getstatuscmp6',
    type: "POST",
    data: {
    sid: sid,
    uid: uid
    },
    cache: false,
    success: function a(result){
    $("#inid").html(result);
    }
  });
});




$('#statusid').on('change', function b() {
$("#mainbox").show();
document.getElementById("ttype").value=2;
var sid = document.getElementById("statusid").value;
var uid = document.getElementById("userid").value;
$.ajax({
url:'<?=base_url();?>Menu/getstatuscmp',
type: "POST",
data: {
sid: sid,
uid: uid
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
});


$('#bylocation').on('change', function b() {
  $("#mainbox").show();
  document.getElementById("ttype").value=3;
  var bylocation = document.getElementById("bylocation").value;
  var uid = document.getElementById("userid").value;
  $.ajax({
    url:'<?=base_url();?>Menu/getcmpbyloc',
    type: "POST",
    data: {
      bylocation: bylocation,
      uid: uid
    },
    cache: false,
    success: function a(result){
      $("#inid").html(result);
    }
  });
});



$('#category').on('change', function b() {
  $("#mainbox").show();
  document.getElementById("ttype").value=4;
  var category = document.getElementById("category").value;
  var uid = document.getElementById("userid").value;

  $.ajax({
    url:'<?=base_url();?>Menu/getcmpbycategory',
    type: "POST",
    data: {
      category: category,
      uid: uid
    },
      cache: false,
      success: function a(result){
      $("#inid").html(result);
    }
  });
});

</script>


<style>

#myInput {
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myUL {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd;
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6;
  padding: 12px;
  text-decoration: none;
  font-size: 18px;
  color: black;
  display: block
}

#myUL li a:hover:not(.header) {
  background-color: #eee;
}
</style>


<script>
function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
</script>


          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
    
    
    
    
    
    
    
    
    </div></div></div>
    
    
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

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWi$dth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
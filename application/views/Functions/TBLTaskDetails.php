<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Task Details | STEM APP | WebAPP</title>
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
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }
      
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php 
    //   echo "dep_name = ".$dep_name;
      $this->load->view($dep_name.'/nav.php');?>
      <style>
       .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}

       .cardline{border-top: 1px solid rgb(4 4 4 / 84%);}
       /* .card .meetingslist-card:hover {
        transition: 0.4s ease-in-out;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset,
              rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset !important;
} */
.live-badge{display:inline-flex;align-items:center;background-color:red;color:#fff;font-weight:700;padding:4px 10px;border-radius:20px;font-size:14px;font-family:Arial,sans-serif;animation:1s infinite pulse;box-shadow:0 0 5px red}.live-dot{width:8px;height:8px;background-color:#fff;border-radius:50%;margin-right:6px;animation:1s infinite blink}@keyframes blink{0%,100%,50%{opacity:1}25%,75%{opacity:0}}@keyframes pulse{0%,100%{box-shadow:0 0 5px red}50%{box-shadow:0 0 20px red}}
    
  p.mb-1 {
    font-size: large;
}  
     .custom-btn {
            /* width: 130px;
            height: 40px; */
            color: #fff;
            border-radius: 5px;
            /* padding: 10px 25px; */
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
            outline: 0;
        }

        .btn-11 {
            border: none;
            background: #212ffb;
            background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
            color: #fff;
            overflow: hidden;
            width: fit-content;
        }

        .btn-11:hover {
            text-decoration: none;
            color: #fff;
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
      
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
        }
    </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">

              <?php if ($this->session->flashdata('success_message')): ?>
                <h5 class="card-header text-center mt-1">
                  <div class="alert alert-success alert-dismissible" role="alert">
                    <?= $this->session->flashdata('success_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                </h5>
              <?php endif; ?>
              <?php if ($this->session->flashdata('error_message')): ?>
                <h5 class="card-header text-center mt-1">
                  <div class="alert alert-danger alert-dismissible" role="alert">
                    <?= $this->session->flashdata('error_message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                </h5>
              <?php endif; ?>

              <style>
                .custom-btn { /* width: 130px; height: 40px; */ color: #fff; border-radius: 5px; /* padding: 10px 25px; */ font-family: Lato, sans-serif; font-weight: 500; background: 0 0; cursor: pointer; transition: .3s; position: relative; display: inline-block; box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px; outline: 0; } .btn-11 { border: none; background: #212ffb; background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%); color: #fff; overflow: hidden; width: fit-content; } .btn-11:hover { text-decoration: none; color: #fff; opacity: .7; } .btn-11:before { position: absolute; content: ''; display: inline-block; top: -180px; left: 0; width: 30px; height: 100%; background-color: #fff; animation: 3s ease-in-out infinite shiny-btn1; } .btn-11:active { box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2); } @keyframes shiny-btn1 { 0% { transform: scale(0) rotate(45deg); opacity: 0; } 80% { transform: scale(0) rotate(45deg); opacity: .5; } 81% { transform: scale(4) rotate(45deg); opacity: 1; } 100% { transform: scale(50) rotate(45deg); opacity: 0; } } .btn-11.partnearray{ background: navy!important; } .btn-11.categoryarray{ background: #ff007f!important; }
                
              </style>

  <?php  
                // dd($taskDatas);

              $taskData             =  $taskDatas[0];
              $task_id              = $taskData->task_id;
              $taskname             = $taskData->taskname;
              $task_user_name       = $taskData->task_user_name;
              $compname             = $taskData->compname;
              $cid                  = $taskData->cid;
              $school_name          = $taskData->school_name;
              $task_school          = $taskData->task_school;
              $spd_request_id       = $taskData->spd_request_id;
              $request_school_name  = $taskData->request_school_name;
              $task_status          = $taskData->task_status;

              $actontaken           = $taskData->actontaken;
              $purpose_achieved     = $taskData->purpose_achieved;
              $remarks              = $taskData->remarks;
              $appointment_datetime = $taskData->appointmentdatetime;
              $initiate_datetime    = $taskData->initiateddt;
              $updated_datetime     = $taskData->updateddate;
              $nextCFID             = $taskData->nextCFID;
              $actiontype_id        = $taskData->actiontype_id;

             if(empty($initiate_datetime)){
                $initiate_datetime = $ted_updated_at;
             }

    
             if($nextCFID != ''){ 
                $lastEntry          = end($taskDatas); // $array is your array variable
                $updated_datetime  = $lastEntry->ted_updated_at;
             }

            // Create DateTime objects
            $dt1 = new DateTime($initiate_datetime);
            $dt2 = new DateTime($updated_datetime);

            // Calculate difference
            $diff = $dt1->diff($dt2);

            // Output difference
            $total_time_in_task =  $diff->format('%a days, %h hours, %i minutes, %s seconds');

            
                ?>









   <div class="card card-primary card-outline" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <div class="card-body box-profile" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center mt-2 mb-2">
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h4>
                      <i style="color: #fa0079ff;">🏷️ Task Details</i> <br>
                      <small>
                       Task Details provide structured information 📋 about assigned activities, including objectives, timelines ⏳, responsibilities 👥, progress tracking ✅, and expected outcomes 🎯, ensuring clarity, accountability, and smooth project execution 🚀.
                      </small>
                    </h4>
                       
                    <hr>
                    <div class="text-center">
                       <h4 style="color: #fc1e89ff;">🏢 <?=$compname?> (<?=$cid?>) </h4>


                        <h5 style="color: #fc1e89ff;">📋 <?=$tasktype?></h5>
                        <h6 style="color: navy;">📝 <?=$taskname?></h6>
                    </div>

                    <hr>
                <div class="row mt-3 mb-2">
                    <div class="col-md-12 text-center">
                      <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                    </div>
                  </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow-lg border-0 rounded-3 m-2" style="background: linear-gradient(to right, #f8f9fa, #ffffff);">
                            <div class="card-body">

                            <h5 class="card-title text-primary">🏷️ <?=$tasktype?></h5>
                            <p class="mb-1">📝 <strong>Task Name:</strong> <?=$taskname?></p>
                            <hr>
                            <p class="mb-1">👷 <strong>Task BY:</strong> <?=$task_user_name?></p>
                            
                            <p class="mb-1">🏢 <strong>Company Name:</strong> 
                                <a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?= $cid ?>">
                                    <?=$compname?> (<?=$cid?>)
                                </a>
                            </p>
                            
               
                         
                           
                        </p>
                         <?php  if($nextCFID != ''){  ?>
                                <p class="mb-1">📂 <strong>Action Taken:</strong> <?=$actontaken?></p>
                                <p class="mb-1">📂 <strong>Purpose Achieved:</strong> <?=$purpose_achieved?></p>
                                <p class="mb-1">📂 <strong>Remarks:</strong> <?=$remarks?></p>
                             <?php } ?>
                           
                            <hr>
                            <p class="mb-1">📊 <strong>Task Status:</strong> <hr>
                                <?php  if($nextCFID != ''){  ?>
                                    <span class="badge bg-success p-2">✔ Completed</span>
                                <?php } else { ?>
                                    <span class="badge bg-warning text-white p-2">⏳ Pending</span>
                                <?php } ?>
                            </p>
                            <hr>
                                <p class="mb-1">📅 <strong>Task Appointment Datetime:</strong> <?=$appointment_datetime?></p>
                                <p class="mb-1">⏱ <strong>Task Initiate Datetime:</strong> <?=$initiate_datetime?></p>
                                <p class="mb-1">🔄 <strong>Task Last Updated Datetime:</strong> <?=$updated_datetime?></p>
                                <p class="mb-1">🕒 <strong>Total Time in Task:</strong> <?=$total_time_in_task?></p>

                            </div>
                        </div>
                    </div>
                        <div class="col-md-4">
                            <div class="card" style="align-items: center; justify-content: center; display: flex; min-height: 100%;">
                              <div>
                                 <h5>📥 Download Activity</h5>
                                <small style="color: #555;">Download Task Activity</small> <hr>
                                <?php if($nextCFID != ''){ ?>
                                  <span 
                                      class="bg-success p-1 text-white" 
                                      style="cursor:pointer; border-radius:5px; transition:0.3s;" 
                                      onmouseover="this.style.backgroundColor='#218838';" 
                                      onmouseout="this.style.backgroundColor='#198754';" 
                                      onclick="downloadFiles('task_id',<?=$task_id?>)">
                                      📥 Download
                                  </span>
                                   <?php }else{ ?> 
                                        <span class="bg-warning p-1 text-white">* Please Wait.. Task is On Going..</span>
                                    <?php } ?>
                              </div>
                            </div>
                        </div>
                       
                </div>



                  </div>
                </div>
                <hr>

                <?php 
                // dd($taskDatas);
                ?>

<div class="table-responsive text-nowrap">
              <table id="example21" class="table table-bordered table-striped table-hover">
                <thead class="table-dark text-center">
                  <tr>
                    <th>🔢 Sr No</th>
                    <?php /*
                    <th>🏷️ Project Code</th>
                    <th>🏫 School Name</th>
                     */ ?>
                    <th>📋 Task Details</th>
                    <th>📋 Task Response</th>
                    <th>📋 Task Attachment</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $k=1; foreach($taskDatas as $row): ?>
                  <tr>
                    <td><?= $k ?></td>

                    <?php /*
                    <td><?= $row->project_code ?></td>
                    <td><a target="_BLANK" href="<?=base_url()?>Menu/SchoolProfileDetails/<?= $row->spd_id ?>"><?= $row->school_name ?></a></td>
                    */ ?>
                    <td><?= $row->taskdetails ?></td>
                    <td><?= $row->task_response ?></td>
                    <td>
                        <?php 
                        $file = $row->attachment_link;

                        // Get file extension
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                        $file = base_url().$file;

                        // Check type and output HTML
                        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                            echo "<img src='$file' alt='Image' style='max-width:200px;'>";
                        } elseif (in_array($ext, ['mp4','webm','ogg'])) {
                            echo "<video controls style='max-width:300px;'>
                                    <source src='$file' type='video/$ext'>
                                    Your browser does not support the video tag.
                                </video>";
                        } elseif (in_array($ext, ['pdf'])) {
                            echo "<iframe src='$file' width='100%' height='400px'></iframe>";
                        } elseif (in_array($ext, ['xls','xlsx','csv'])) {
                            echo "<a href='$file' target='_blank'>📊 Excel File</a>";
                        } elseif (in_array($ext, ['doc','docx'])) {
                            echo "<a href='$file' target='_blank'>📝 Word File</a>";
                        } else {
                            // echo "<a href='$file' target='_blank'>📁 Download File</a>";
                        }
                        ?>
                </td>
                  </tr>
                  <?php $k++; endforeach; ?>
                </tbody>
              </table>
            </div>

<?php // dd($taskDatas); ?>

            <hr>
           <div class="table-responsive text-nowrap">
                  <table id="example111" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col" class="text-white">🔢 Sr. No.</th>
                        <th scope="col" class="text-white">📂 Task Name</th>
                        <th scope="col" class="text-white">📝 Task Details</th>
                        <th scope="col" class="text-white">🚦 Stage</th>
                        <th scope="col" class="text-white">🚦 Complete Status</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $j =1;
                        foreach($getCompleteTaskDetails as $data){
                            
                       $indicator = $data->taskdetails;

                        // Check existence using anonymous function
                        $found = count(array_filter($taskDatas, function($item) use ($indicator) {
                            return $item->taskdetails === $indicator;
                        })) > 0;

                        // Set style and emoji
                        if ($found) {
                            $style = "style='color:green!important;'";
                            $emoji = "✅"; // green check
                        } else {
                            $style = "style='color:red!important;'";
                            $emoji = "❌"; // red cross
                        }

                            ?>
                      <tr >
                        <td <?=$style?>><?= $j; ?></td>
                        <td <?=$style?>><?= $data->taskname; ?></td>
                        <td <?=$style?>>
                            <?= $data->taskdetails; ?>
                        </td>
                        <td <?=$style?>><?= $data->taskaction; ?></td>
                        <td><?= $emoji; ?></td>
                      </tr>
                      <?php $j++; } ?>
                    </tbody>
                  </table>
                </div>
              


                

              </div>
            </div>





























            <!-- /.card-body -->
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
 


    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>

$(document).ready(function() {


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>


  function downloadFiles(stype, value) {
    var btn = event.target;
    var oldText = btn.innerHTML;

    btn.innerHTML = "⏳ Please wait…";
    btn.style.pointerEvents = "none";

    // Redirect (this will trigger CI zip->download)
    window.location.href = "<?=base_url();?>Menu/GetAllAttchementDownload?stype=" + stype + "&value=" + value;
    // Reset button text after 8s
    setTimeout(function(){
        btn.innerHTML = oldText;
        btn.style.pointerEvents = "auto";
    }, 8000);
}



      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $("#example2").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');


      $(document).ready(function () {
        $('#downloadPdf').click(function () {
          const button = $(this);
          button.prop('disabled', true).text('Please wait...');

          const { jsPDF } = window.jspdf;
          const doc = new jsPDF('p', 'mm', 'a4');
          const content = document.querySelector('.content-wrapper');

          html2canvas(content, {
            scale: 2,
          }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;

            let heightLeft = imgHeight;
            let position = 0;

            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
              position -= pageHeight;
              doc.addPage();
              doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
              heightLeft -= pageHeight;
            }

            doc.save('dashboard.pdf');
            // Revert button
            button.prop('disabled', false).text('Download PDF');
          }).catch(error => {
            console.error('PDF generation failed:', error);
            button.prop('disabled', false).text('Download PDF');
          });
        });
      });



    </script>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meeting Details | STEM APP | WebAPP</title>
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            width: 100%;
        }

      .scrollme {
        overflow-x: auto;
      }
    tr{
      font-weight: bold;
    }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
    <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                    <div class="frame-layer-3">
                      <h1 style="color: #ff006c;">Meeting Task Details</h1>
                      <p class="premium-color-2" style="color: #ff0000;">This section provides a comprehensive overview of all meetings, including statistics on various types of meetings, their statuses, and other relevant details.</p>
                    </div>
                    <hr>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
$mapDatas = $this->Menu_model->GetMeetingDetailsByMeetingsID($tmeeting_taskid);

if(sizeof($mapDatas) > 0){
      $location = $mapDatas[0]; 

      $initiateLat    = $location->initiateLat;
      $initiateLongi  = $location->initiateLongi;

      $slatitude      = $location->slatitude;
      $slongitude     = $location->slongitude;

      $clatitude      = $location->clatitude;
      $clongitude     = $location->clongitude;

      // Define the coordinates

      
      // Check if $initiateLat is empty
      if (empty($initiateLat)) {
        // If $initiateLat is empty, use $slatitude and $slongitude as the origin
        $originCoords = "$slatitude,$slongitude";
      } else {
        // Otherwise, use $initiateLat and $initiateLongi as the origin
        $originCoords = "$initiateLat,$initiateLongi";
      }

      // Define the waypoints and destination coordinates
      $waypointsCoords = "$slatitude,$slongitude";
      $destinationCoords = "$clatitude,$clongitude";

      // Construct the Google Maps URL
      $googleMapsUrl = "https://www.google.com/maps/dir/?api=1&origin=$originCoords&waypoints=$waypointsCoords&destination=$destinationCoords";



 } ?>
 
 


        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">

    

            <div class="body-content text-center">
         
    
          
<?php

        $colors = array(
            'bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning',
            'bg-info', 'bg-dark', 'bg-light'
        );

        $textColors = array(
            'bg-primary' => 'text-white',
            'bg-secondary' => 'text-white',
            'bg-success' => 'text-white',
            'bg-danger' => 'text-white',
            'bg-warning' => 'text-dark',
            'bg-info' => 'text-white',
            'bg-dark' => 'text-white',
            'bg-light' => 'text-dark'
        );

        $cluster_id     = $totalMeetingsUserByDatas[0]->cluster_id;
        $cluster_name   = $totalMeetingsUserByDatas[0]->cluster_name;
        $cluster_travel = $totalMeetingsUserByDatas[0]->cluster_travel;


        foreach ($totalMeetingsUserByDatas as $item) {

            $itemcid = $item->cid;
            $momtask_mom = '';
            $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($tmeeting_taskid);
         
            if(sizeof($momtaskDatas) > 0){
               $momtask_id = $momtaskDatas[0]->id;
               $momtask_mom = $momtaskDatas[0]->mom;
               $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);
               if(sizeof($momdatas) > 0){
                  $mom_id = $momdatas[0]->id;
                  $momatag =  '<a class="p-1 bg-success" target="_BLANK" href="' . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $itemcid . '">view</a>'; 
                  $iframedata = '<iframe 
                    src="' . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $itemcid . '" 
                    width="100%" 
                    height="700" 
                    frameborder="2">
                </iframe>';
               }else{
                $momatag =  "<span class='p-1 bg-warning'>Pending For Write</span>";
               }
            }else{
                $momatag =  "NA";
            }

            $colorIndex = 0;
            echo '<div class="row">';

           
            foreach ($item as $key => $value) {

            
                if($key == 'meetings_id' || $key == 'cluster_id' || $key == 'nextCFID' || $key =='assignedto_id' || $key == 'actiontype_id' || $key == 'filter_by' || $key == 'task_user_id' || $key == 'task_id' || $key == 'cid_id' || $key == 'initphoto' || $key == 'cphoto' || $key == 'actontaken' || $key == 'purpose_achieved'){
                    continue;
                }
               
                
                $color = $colors[$colorIndex % count($colors)];
                $textColor = $textColors[$color];
                echo '<div class="col-md-6 mb-3">';
                echo '<div class="card ' . $color . ' ' . $textColor . ' h-100">';
                echo '<div class="card-header">' . htmlspecialchars(ucfirst(str_replace('_', ' ', $key))) . '</div>';
                echo '<div class="card-body">';

                
                if($key == 'mom_remarks'){
                    $value = $momtask_mom;
                }
               
                if($key == 'task_approved_status'){
                    if($value == 1){
                        $value = 'Approved';
                    }
                }
                if($key == 'cluster_name'){
                    if($value !== ''){
                      $value = '<a href="' . base_url() . 'Menu/TravelClusterDetailsByID/' . $cluster_id . '" target="_BLANK">'
                      . $cluster_name . ' ( ' . $cluster_travel . ' )</a>';
                    }
                }


                echo '<p class="card-text">' . $value . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                $colorIndex++;
            }
            echo '</div>';
        }

        echo ' <hr/><div class="photo">'; ?>


<div class="row">
<div class="col-md-12 text-center">
            <br>
            <hr>
            <?php
$data = $totalMeetingsUserByDatas[0]; // Replace with your actual data array

$description = <<<EOD
<style>
    .desc-container { font-family: Arial, sans-serif; line-height: 1.6; }
    .highlight { font-weight: bold; color: #2c3e50; }
    .username { color: #1abc9c; font-weight: bold; }
    .company { color: #2980b9; font-weight: bold; }
    .date { color: #8e44ad; font-weight: bold; }
    .status { color: #d35400; font-weight: bold; }
    .approved { color: #27ae60; font-weight: bold; }
    .key { background: #f1c40f; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
    .key1 { background:rgba(208, 255, 0,.4); padding: 2px 6px; border-radius: 4px; font-weight: bold; }
</style>

<div class="desc-container p-5">
    
<hr>
<h4 class='key1 p-1'><span class="highlight">Meeting Task Summary for</span> <span class="username">{$data->task_username}</span></h4>
<hr>

    <p>A scheduled meeting was conducted by <span class="username">{$data->task_username}</span> on behalf of 
    <span class="company">{$data->compname}</span> (<span class="highlight">Company ID:</span> {$data->cid}). 
    The meeting was originally planned on <span class="date">{$data->fwd_date}</span> and successfully took place on 
    <span class="date">{$data->appointmentdatetime}</span>.</p>

    <p class="key">{$momtask_mom}</p>

    <p>This task was marked as status is <span class="status">{$data->current_status}</span> and Meetings Type is  
    <strong>{$data->mtype}</strong> (Right Person), selected as "<strong>{$data->selectby}</strong>".</p>

    <p>The meeting was <span class="approved">approved</span> by <strong>{$data->task_approved_by}</strong>. 
    It officially started at <span class="date">{$data->startm}</span> and closed at <span class="date">{$data->closem}</span>.</p>

    <p>Meeting status is now <span class="status">{$data->meetings_status}</span>. Although it is closed, the result remains 
    <span class="status">tentative</span> based on the client's final decision.</p>

</div>
EOD;

?>

<div class="card">
  <?php echo $description; ?>
  <hr>
<h5>  <a href="<?php echo htmlspecialchars($googleMapsUrl); ?>" target="_blank">Open Location in Google Maps</a></h5>
<hr>
</div>
       
            <br>
            
            </div>

</div>


       <?php  
      //  echo $item->initphoto;
       $initphoto =  base_url().'uploads/day/'.htmlspecialchars($data->initphoto);
       $encoded_url = htmlspecialchars(str_replace(' ', '%20', $initphoto), ENT_QUOTES, 'UTF-8');
      //  echo  $encoded_url;

       echo '<h2>📸 Meeting Photos</h2><hr/>'; ?> 
        <div class="row">
            <div class="col-md-6 text-center">
                <div class="card" style="align-items: center;">
                   <hr>
                    <h4>📸 Meeting Initiate Photo</h4>
                    <p>📸 Meeting Initiate Photo captures the moment a meeting begins, showing attendees ready to collaborate 🤝, share ideas 💡, and set goals 🎯 for a productive and engaging discussion.</p>
                    <hr>
                    <?php echo '<img src="' . $encoded_url . '" width="200px" alt="Start Photo" class="img-fluid mb-3">'; ?>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <div class="card" style="align-items: center;">
                    <hr><h4>📸 Meeting Start Photo</h4>
                    <p>📸 Meeting Start Photo captures the energized moment when discussions kick off 💬, ideas flow 💡, and teamwork 🤝 begins, marking the official start of a focused and productive meeting session.</p><hr>
                    <?php echo '<img src="' . base_url().htmlspecialchars($data->cphoto) . '" width="200px" alt="Closing Photo" class="img-fluid">'; ?>
                </div>
            </div>
            <div class="col-md-12 text-center">
            <br>
            <br>
                <hr>
                <h4>View Mom Details</h4>
                <hr>
                <?=$momatag ?>
                <hr>
                <?=$iframedata ?>
            </div>
        </div>

        <br>
        <br>
        <br>
        <br>
        
        <?php
        echo '</div>';
        ?>
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
      <script>
        $("#example1").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      </script>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const cards = document.querySelectorAll('.card');

          cards.forEach(card => {
            const bgColor = window.getComputedStyle(card).backgroundColor;
            const rgb = bgColor.match(/\d+/g);
            const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;

            if (brightness > 128) {
              card.classList.add('text-dark');
            } else {
              card.classList.add('text-light');
            }
          });
        });
      </script>
    </body>
</html>

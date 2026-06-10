<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Todays Autotask Time | WebAPP</title>
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
  <script>
      function validateTimeInputAuto(event) {
          const input = event.target;
          const timeValue = input.value;
          const minTime = "16:00";
          const maxTime = "19:00";
      
          if (timeValue < minTime || timeValue > maxTime) {
              alert("Please enter a time between 04:00 PM and 7:00 PM.");
              input.value = "";
          }
      }
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('start-time');
          timeInput.setAttribute('min', '16:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInputAuto);
      });
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('end-time');
          timeInput.setAttribute('min', '16:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInputAuto);
      });
</script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Preloader -->
  
  <!-- Navbar -->
  <?php require('nav.php');?>
  <!-- /.navbar -->
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
<!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            
    <?php if ($this->session->flashdata('success_message')): ?>
          <div class="container-fluid">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong> 
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          </div>
        <?php endif; ?>
          
        <form action="<?=base_url();?>Menu/AddAutoTaskTime" method="post" >
                  <center>
                
                    <input type="hidden" name="autotasktimeisset" value="<?=$gecurAutoTaskTime?>">
               
                      <hr class="hrclass" style="width: 600px;"/>
                    <div class="card">
              <div class="card-body" id="mainboxAutoTask1">
                <h5><i>Today's Auto Task and Planned Time</i></h5>
                <hr/>
               
                <div class="row">
                  
                <div class="col-sm-6 col-sm mt-3">
                <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                  <h6> Auto task time should be between 4:00 PM to 7:00 PM and maximum duration of 90 minutes. </h6>
                </marquee>
                  <div class="form-group">
                    <label for="start-time">Enter Start Time</label>
                    <input type="time" id="start-time" name="startautotasktime" class="form-control is-invalid" required>
                  </div>
                  <div class="form-group">
                    <label for="end-time">Enter End Time</label>
                    <input type="time" id="end-time" name="endautotasktime" class="form-control is-invalid" required>
                  </div>
                  </div>
                  <div class="col-sm-6 col-sm mt-3">
                  <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                  <h6>Plan Your day for Today,You will get max 1 hour to plan all the tasks for the day.</h6>
                </marquee>
                      <div class="form-group">
                        <label for="end-time">Today's Planner Time start.</label>
                        <input type="time" readonly id="start_tttpft" name="start_tttpft" class="form-control is-invalid" required>
                      </div>
                      <div class="form-group">
                        <label for="end-time">Today's Planner Time End.</label>
                        <input type="time" readonly id="end_tttpft" name="end_tttpft" class="form-control is-invalid" required>
                      </div>
                  </div>
                </div>
              </div>
            </div>
                  </center>
                  <br>
                  <div class="col1 text-center">
                    <button type="submit" class="btn btn-danger">Add Todays Auto Task Time</button>
                  </div>
                </form>
 
        </div>     
    </section>
  
  
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type='text/javascript'>
</script>
          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
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
<script>
      $(document).ready(function() {
    
          $('#end-time').on('change', function() {
              var startTime = $('#start-time').val();
              if (startTime === '') {
                  alert("Please Enter Start Time");
                  $('#end-time').val('');
              } else {
                  var endTime = $(this).val();
                  var startTimeMinutes = convertTimeToMinutes(startTime);
                  var endTimeMinutes = convertTimeToMinutes(endTime);
                  // Check if the difference is more than 90 minutes
                  if ((endTimeMinutes - startTimeMinutes) > 90) {
                      alert('Auto Task Max Time is Only 90 Minutes');
                      $('#end-time').val('');
                  }
              }
          });
          function convertTimeToMinutes(time) {
                          var timeParts = time.split(':');
                          var hours = parseInt(timeParts[0], 10);
                          var minutes = parseInt(timeParts[1], 10);
                          return (hours * 60) + minutes;
                      }
        $('#end-time').on('change', function() {
        let endTime = $(this).val();
        if (endTime) {
            // Convert endTime to a Date object
            let endDateTime = new Date('1970-01-01T' + endTime + ':00');
            // Increment by 1 minute for start_tttpft
            // let startDateTime = new Date(endDateTime.getTime() + 1 * 60000);
            let startDateTime = new Date(endDateTime.getTime() + 0 * 60000);
            let startHours = ('0' + startDateTime.getHours()).slice(-2);
            let startMinutes = ('0' + startDateTime.getMinutes()).slice(-2);
            $('#start_tttpft').val(startHours + ':' + startMinutes);
            // Increment by 1 hour for end_tttpft
            let endTttPftDateTime = new Date(endDateTime.getTime() + 1 * 3600000);
            let endTttPftHours = ('0' + endTttPftDateTime.getHours()).slice(-2);
            let endTttPftMinutes = ('0' + endTttPftDateTime.getMinutes()).slice(-2);
            $('#end_tttpft').val(endTttPftHours + ':' + endTttPftMinutes);
        }
    });
  
      });
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
</body>
</html>

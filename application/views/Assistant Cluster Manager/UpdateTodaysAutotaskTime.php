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
    <style>
       body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #eef2ff, #f8fafc);
      min-height: 100vh;
    }

    .planner-card {
      border: none;
      border-radius: 18px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.08);
      background: #ffffff;
    }

    .section-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-banner {
      background: linear-gradient(90deg, #ffe4e6, #fff1f2);
      border-left: 5px solid #ef4444;
      border-radius: 12px;
      padding: 12px 16px;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    label {
      font-weight: 500;
      margin-bottom: 6px;
    }

    .form-control {
      border-radius: 12px;
      padding: 10px 14px;
    }

    .form-control:focus {
      border-color: #6366f1;
      box-shadow: 0 0 0 0.15rem rgba(99,102,241,0.25);
    }

    .btn-submit {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      border: none;
      border-radius: 14px;
      padding: 12px 26px;
      font-weight: 600;
      box-shadow: 0 10px 20px rgba(239,68,68,0.35);
    }

    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 14px 28px rgba(239,68,68,0.45);
    }

    .divider {
      height: 1px;
      background: linear-gradient(to right, transparent, #e5e7eb, transparent);
      margin: 24px 0;
    }
    </style>
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
      <?php $this->load->view($dep_name.'/nav.php');?>
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


<div class="container py-5">
  <form action="<?= base_url(); ?>Menu/AddAutoTaskTime" method="post">

    <input type="hidden" name="autotasktimeisset" value="<?= $gecurAutoTaskTime ?>">

    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-9">
        <div class="planner-card p-4 p-md-5">

          <h4 class="text-center mb-4">🗓️ Today's Auto Task & Planner Time</h4>

          <div class="divider"></div>

          <div class="row g-4">

            <!-- Auto Task Time -->
            <div class="col-md-6">
              <div class="section-title">⏱️ Auto Task Time</div>

              <div class="info-banner">
                Auto task time must be between <strong>4:00 PM – 7:00 PM</strong> with a maximum duration of <strong>90 minutes</strong>.
              </div>

              <div class="mb-3">
                <label for="start-time">Start Time</label>
                <input type="time" id="start-time" name="startautotasktime" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="end-time">End Time</label>
                <input type="time" id="end-time" name="endautotasktime" class="form-control" required>
              </div>
            </div>

            <!-- Planner Time -->
            <div class="col-md-6">
              <div class="section-title">🧠 Day Planning Window</div>

              <div class="info-banner" style="border-left-color:#6366f1;background:linear-gradient(90deg,#eef2ff,#f5f7ff)">
                You get a maximum of <strong>1 hour</strong> to plan all tasks for today.
              </div>

              <div class="mb-3">
                <label for="start_tttpft">Planner Start Time</label>
                <input type="time" readonly id="start_tttpft" name="start_tttpft" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="end_tttpft">Planner End Time</label>
                <input type="time" readonly id="end_tttpft" name="end_tttpft" class="form-control" required>
              </div>
            </div>

          </div>

          <div class="divider"></div>

          <div class="text-center">
            <button type="submit" class="btn btn-submit text-white">✅ Save Today's Auto Task Time</button>
          </div>

        </div>
      </div>
    </div>

  </form>
</div>



          </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type='text/javascript'></script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
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
                 if ((endTimeMinutes - startTimeMinutes) > 90 || (endTimeMinutes - startTimeMinutes) < 90) {
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
        // if (endTime) {
        //     // Convert endTime to a Date object
        //     let endDateTime = new Date('1970-01-01T' + endTime + ':00');
        //     // Increment by 1 minute for start_tttpft
        //     // let startDateTime = new Date(endDateTime.getTime() + 1 * 60000);
        //     let startDateTime = new Date(endDateTime.getTime() + 0 * 60000);
        //     let startHours = ('0' + startDateTime.getHours()).slice(-2);
        //     let startMinutes = ('0' + startDateTime.getMinutes()).slice(-2);
        //     $('#start_tttpft').val(startHours + ':' + startMinutes);
        //     // Increment by 1 hour for end_tttpft
        //     let endTttPftDateTime = new Date(endDateTime.getTime() + 1 * 3600000);
        //     let endTttPftHours = ('0' + endTttPftDateTime.getHours()).slice(-2);
        //     let endTttPftMinutes = ('0' + endTttPftDateTime.getMinutes()).slice(-2);
        //     $('#end_tttpft').val(endTttPftHours + ':' + endTttPftMinutes);
        // }
      
      
        if (endTime) {
              // Convert endTime to a Date object
              let endDateTime = new Date('1970-01-01T' + endTime + ':00');
      
              // Increment by 0 minutes for start_tttpft (same as endTime)
              let startDateTime = new Date(endDateTime.getTime() + 0 * 60000);
              let startHours = ('0' + startDateTime.getHours()).slice(-2);
              let startMinutes = ('0' + startDateTime.getMinutes()).slice(-2);
              $('#start_tttpft').val(startHours + ':' + startMinutes);
      
              // Set end_tttpft to a fixed time of 19:00
              let endTttPftDateTime = new Date('1970-01-01T19:00:00');
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
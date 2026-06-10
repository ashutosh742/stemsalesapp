<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Early Planner Request | STEM APP | WebAPP</title>

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/jqvmap.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/daterangepicker.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= base_url(); ?>assets/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css">

  <!-- Custom CSS -->
  <style>
    .scrollme { overflow-x: auto; }
    .card-bg-1 { background-color: gold !important; }
    .card-bg-2 { background-color: silver !important; }
    .early-image { max-width: 400px; }
    .beautiful-textarea {
      padding: 10px;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      font-size: 16px;
      resize: none;
      outline: 0;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .beautiful-textarea:focus {
      border-color: #66afe9;
      box-shadow: 0 4px 12px rgba(102, 175, 233, 0.4);
    }
    .custom-btn {
      color: #fff;
      border-radius: 5px;
      font-family: Lato, sans-serif;
      font-weight: 500;
      background: none;
      cursor: pointer;
      transition: 0.3s;
      position: relative;
      display: inline-block;
      box-shadow: inset 2px 2px 2px 0 rgba(255, 255, 255, 0.5), 7px 7px 20px 0 rgba(0, 0, 0, 0.1), 4px 4px 5px 0 rgba(0, 0, 0, 0.1);
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
      opacity: 0.7;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Navbar -->
    <?php $this->load->view($dep_name . '/nav.php'); ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <!-- Content Header -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-md-12 text-center">
              <div class="frame-layer-1">
                <div class="frame-layer-2">
                  <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
                    <h1 class="m-0 premium-color-1" style="color: #ff009b;">📅✨ Early Planner Request 📝⏰</h1>
                    <center>
                      <p class="premium-color-2" style="color: #ff0000; width: 60%;">
                        Start your day with clarity using an 📝 Early Planner Request! 🌅 Submit your tasks and priorities early to stay ahead of the curve. ✅ Boost productivity, reduce last-minute stress 😌, and ensure smooth planning for your team 🧠💼. Early requests help align goals 📈 and improve task flow 🚀. Plan smarter, act faster, and get things done efficiently! 🔄🕒 #StayOrganized #PlanAhead
                      </p>
                    </center>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <hr>
      <section class="content">
        <div class="container-fluid">
          <div class="card text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
            <div class="row" style="color: black;">
              <div class="col-md-6">
                <div style="justify-content: center; display: flex; align-items: center; height: 400px;">
                  <div>
                    <?php

                    // dd($checkTodaysEarlyData);
                   
                    if(sizeof($checkTodaysEarlyData) == 0){

                    foreach ($checkUserPlannerTime as $item) {
                      $start_tttpft = $item->start_tttpft;
                      $dataas = "<p>🧠 Your Tomorrow Work Planner Start ⏰ is set to this date: <strong>" . $item->date . date("g:i A", strtotime($item->start_tttpft)) . "</strong>, and the End Time 🔚 is set to <strong>" . $item->date . date("g:i A", strtotime($item->end_tttpft)) . "</strong>. This schedule helps you pre-plan for the next day and allocate focused work time efficiently. ✅ Please explain why you would like to change the Tomorrow Work Planner Start Time.</p>";
                    
                      $pargraph = "Our Planner Start is set to " . date("g:i A", strtotime($item->start_tttpft)) . ", and the End Time is set to " . date("g:i A", strtotime($item->end_tttpft)) . ". This schedule helps you pre-plan for the next day and allocate focused work time efficiently. Please explain why you would like to change the Tomorrow Work Planner Start Time.";

                      $pargraph1 = "🧠 Your Tomorrow Work Planner Start ⏰ is set to " . date("g:i A", strtotime($item->start_tttpft)) . ", and the End Time 🔚 is set to " . date("g:i A", strtotime($item->end_tttpft)) . ". This schedule helps you pre-plan for the next day and allocate focused work time efficiently.";
                    }
                   
                    // if (strtotime(date('H:i')) > strtotime($start_tttpft)) { echo $pargraph1; } else {
                        echo  $dataas;
                        ?>
                    <form action="<?= base_url() ?>/Menu/SubmitEarlyPlannerRequest" method="POST">
                      <div class="form-group">
                        <input type="hidden" value="<?= $pargraph ?>" name="pre_message">
                        <input type="hidden" value="<?= $start_tttpft ?>" name="old_time">
                      </div>
                      <div class="form-group">
                        <label for="exampleFormControlTextarea1">❓ Why do you need to start your Tomorrow Work Planner earlier than the scheduled time?</label>
                        <textarea class="form-control" name="user_message" rows="3" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="pre_time">Select Time (Between <?= date('H:i') ?> and <?= $start_tttpft ?>):</label>
                        <input class="form-control" type="time" id="pre_time" min="<?= date('H:i') ?>" max="<?= $start_tttpft ?>" name="new_time" required>
                      </div>
                      <center><button class="custom-btn btn-11 partnearray p-2">Create Request</button></center>
                    </form>
                       
                    <?php // } ?>
                    <?php  }else{  $req = $checkTodaysEarlyData[0]; // Assuming your array is stored in $data ?> 
                            <div style="margin: 20px auto; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; background: #f9f9f9; font-family: Arial;">
                                <h2 style="margin-top: 0;">📅 <?= $req->request_type ?></h2>
                                <p><strong>👤 Requested By:</strong> <?= $req->request_username ?></p>
                                <p><strong>🗓 Request Date:</strong> <?= $req->request_date ?></p>
                                <p><strong>📝 Message:</strong> <?= $req->pre_request_message ?></p>
                                <p><strong>✍️ User Remarks:</strong> <?= $req->request_message ?></p>
                                <p><strong>⏰ Old Time:</strong> <?= $req->old ?> <strong>➡️ New Time:</strong> <?= $req->new ?></p>
                                <p>
                                    <strong>✅ Approval Status:</strong>
                                    <?= $req->approved_status == 1 ? 'Approved 🟢' : 'Pending 🕒' ?>
                                </p>
                                <?php if ($req->approved_status == 1): ?>
                                    <p><strong>👨‍💼 Approved By:</strong> <?= $req->request_approved_name ?></p>
                                    <p><strong>📆 Approved On:</strong> <?= $req->approved_date ?></p>
                                <?php endif; ?>
                                <p style="font-size: 12px; color: #888;">🕐 Created at: <?= $req->created_at ?></p>
                            </div>
                        <?php } ?>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="carhdsfd">
                  <div class="img-responsive" style="justify-content: center; display: flex; align-items: center;">
                    <img class="early-image" src="<?= base_url() ?>/uploads/task/early_planner-min.png" class="img-fluid" alt="early planner request">
                  </div>
                </div>
              </div>
            </div>
             <br>
          <br>
          <br>
          <br>
          </div>
         
         
        </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?= date("Y") ?> <a href="<?= base_url(); ?>">Stemlearning</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
          <b>Version</b> 1.0
        </div>
      </footer>
    </div>
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery-ui.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/Chart.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/sparkline.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery.vmap.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery.vmap.usa.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery.knob.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/moment.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/daterangepicker.js"></script>
  <script src="<?= base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/summernote-bs4.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery.overlayScrollbars.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/dataTables.responsive.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/responsive.bootstrap4.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/buttons.bootstrap4.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/jszip.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/pdfmake.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/vfs_fonts.js"></script>
  <script src="<?= base_url(); ?>assets/js/buttons.html5.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/buttons.print.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/buttons.colVis.min.js"></script>
  <script src="<?= base_url(); ?>assets/js/adminlte.js"></script>
  <script src="<?= base_url(); ?>assets/js/dashboard.js"></script>

  <!-- Custom JavaScript -->
  <script>

    $.widget.bridge('uibutton', $.ui.button);

    $("#example10").DataTable({
      "responsive": false,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["csv", "excel", "pdf"]
    }).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');

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

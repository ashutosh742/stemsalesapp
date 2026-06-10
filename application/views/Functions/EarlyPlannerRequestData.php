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
    tr,td,th{
      font-weight: 500;
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
                    <h1 class="m-0 premium-color-1" style="color: #ff009b;">📅✨ Early Planner Request By User 📝⏰</h1>
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

      <section class="content">
        <div class="container-fluid">
        <?php if ($this->session->flashdata('pending_message')): ?>
            <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?= $this->session->flashdata('pending_message'); ?>
        </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?= $this->session->flashdata('success_message'); ?>
        </div>
        <?php endif; ?>
        <?php
        if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <?php endif; ?>
          <hr>
          <div class="card text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">

            <hr>
            <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px;">
              <div class="table-responsive text-nowrap">
                <table id="example10" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                   <tr>
                    <th>🆔 ID</th>
                    <th>🙋 Request Username</th>
                    <th>📅 Request Date</th>
                    <th>📝 Request Type</th>
                    <th>🧠 Pre Request Message</th>
                    <th>✍️ Request Message</th>
                    <th>⏰ Old Time</th>
                    <th>⏱ New Time</th>
                    <th>✅ Approved Status</th>
                    <th>👨‍💼 Approved By</th>
                    <th>📆 Approved Date</th>
                    <th>🕒 Created At</th>
                    <th>🙋 Action</th>
                    
                </tr>

                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    foreach ($checkTodaysEarlyData as $item) {
                      $r = rand(150, 255);
                      $g = rand(150, 255);
                      $b = rand(150, 255);
                      $backgroundColor = "rgba($r, $g, $b, 0.2)";
                      $hue = rand(0, 360);
                      $saturation = rand(60, 100);
                      $lightness = rand(30, 45);
                      $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                    ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>; color: <?php echo $backgroundColorNew; ?> !important">
                       <td><?= $i ?></td>
                       <td><?= htmlspecialchars($item->request_username) ?></td>
                        <td><?= htmlspecialchars($item->request_date) ?></td>
                        <td><?= htmlspecialchars($item->request_type) ?></td>
                        <td><?= htmlspecialchars($item->pre_request_message) ?></td>
                        <td><?= htmlspecialchars($item->request_message) ?></td>
                        <td><?= htmlspecialchars($item->old) ?></td>
                        <td><?= htmlspecialchars($item->new) ?></td>
                        <td><?= htmlspecialchars($item->approved_status) ?></td>
                                    <td><?= htmlspecialchars($item->request_approved_name) ?></td>
                        <td><?= htmlspecialchars($item->approved_date) ?></td>
                        <td><?= htmlspecialchars($item->created_at) ?></td>

                           <td>
                                                  
                              <?php 
                          if($item->approved_status == 0){ ?>
                  
                          <div>
                            <p><button type="button" class="btn btn-success"  onclick="TakeAction(<?= $item->id?>)">Action</button></p>
                          </div>
                  
                          <?php }else if($item->approved_status == 1){ ?>
                          <span class="p-1 bg-success mr-2">Approved</span>
                          <?php }else if($item->approved_status == 2){ ?>
                          <span class="p-1 bg-danger mr-2">Reject</span>
                          <?php } ?>
                            
                        </td>

                      </tr>
                    <?php
                      $i++;
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        </div>

        <!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Early Planner Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
           <form action="<?= base_url() ?>/Menu/ActionEarlyPlannerRequest" method="POST">
                      <div class="form-group">
                        <input type="hidden" id="action_id" value="" name="action_id">
                      </div>
                      <div class="form-group">
                        <select class="form-control" name="action_status">
                            <option selected disabled>--Select--</option>
                            <option value="1">Approved</option>
                            <option value="2">Reject</option>
                          </select>
                      </div>
                      <center><button class="custom-btn btn-11 partnearray p-2">Submit</button></center>
                    </form>


      </div>
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

  <script>
     function TakeAction(id){
              $('#exampleModalCenter').modal('show');
              $('#action_id').val(id);
            }
  </script>


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
    $(document).ready(function() {
      $('#pre_time').timepicker({
        timeFormat: 'HH:mm',
        interval: 1,
        minTime: '<?= date("H:i") ?>',
        maxTime: '<?= $start_tttpft ?>',
        startTime: '<?= date("H:i") ?>',
        dynamic: false,
        dropdown: true,
        scrollbar: true
      });
    });

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

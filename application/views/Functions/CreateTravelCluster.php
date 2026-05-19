<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Travel Cluster | STEM APP | WebAPP</title>
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
      background: url('https://example.com/classic-background.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .card {
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
      transition: 0.3s;
      border-radius: 5px;
    }
    .card:hover {
      box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    }
    .content-wrapper {
      background-color: rgba(255, 255, 255, 0.9);
    }
    .bg-primary {
      background-color: #007bff !important;
    }
    .form-control {
      border-radius: 5px;
    }
    .btn-primary {
      border-radius: 5px;
    }
     tr,td{
        font-weight: 500;
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
            <div class="col-sm-6">
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <h4></h4>
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
         

                   <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                      <h3 class="m-0 premium-color-1 text-center p-4" style="color: #ff009b;font-family: sans-serif;">Create Travel Cluster</h3>
                      <center>
                            <p style="width: 70%;">To create a travel cluster, identify key destinations with unique attractions. Group nearby locations to optimize travel routes. Include diverse experiences like cultural sites, natural wonders, and urban centers. Ensure accessibility and accommodation options. Highlight local cuisine and activities. Aim for a mix of relaxation and adventure to cater to various traveler preferences.</p>
                        </center>
                        <br>
                    </div>


          <hr>
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
          <div class="row p-3">
            <div class="col-md-6 col-lg-6">
              <div class="card card-primary card-outline" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="card-body box-profile">
                  <form action="<?=base_url();?>Menu/AddNewCluster" method="post" enctype="multipart/form-data">
                    <div class="was-validated">
                      <input type="hidden" name="uid" value="<?=$uid?>">
                      <div class="form-group">
                        <label for="task_type">Select BD</label><br>
                        <select class="form-control" name="selcted_bd[]" id="selcted_bd" multiple required>
                          <option value="" Selected disabled>Select BD</option>
                          <?php foreach($totalTeams as $totalTeam): ?>
                          <option value="<?=$totalTeam->user_id;?>"><?=$totalTeam->name;?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="task_type">Type of Travel</label><br>
                        <select class="form-control" name="typeofTravel" id="typeofTravel" required>
                          <option value="" Selected disabled>Select Location</option>
                          <option value="base">Base Location</option>
                          <option value="outStation">Out Station</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="task_type">Travel Cluster Name</label><br>
                        <input type="text" placeholder='Enter Cluster Name' class="form-control" name="cluster" id="cluster" required>
                      </div>
                      <div class="form-group">
                        <label for="task_type">Select Cluster State</label><br>
                        <select name="clusterState" id="clusterState" class="form-control" required onchange="countSelected('clusterState','selectedCount')">
                        <option value='' disabled selected>Select State</option>
                          <?php foreach($state as $stat){?>
                          <option value='<?=$stat->state_id ?>'><?=$stat->state_title ?></option>
                          <?php }?>
                        </select>
                         <p id="selectedCount">Selected: 0</p>
                      </div>
                      <div class="form-group">
                        <label for="task_type">Select Cluster District</label><br>
                        <select name="clusterDistrict[]" id="clusterDistrict" multiple class="form-control" required onchange="countSelected('clusterDistrict','clusterDistrictCount')">
                        </select>
                        <p id="clusterDistrictCount">Selected: 0</p>
                      </div>
                      <div class="form-group">
                        <label for="task_type">Select Cluster City</label><br>
                        <select name="clusterCity[]" multiple id="clusterCity" class="form-control" required onchange="countSelected('clusterCity','clusterCityCount')" style="height:300px;">
                        </select>
                        <p id="clusterCityCount">Selected: 0</p>
                      </div>
                      <hr>
                       <div class="form-group">
                        <label for="task_type">BD Remarks</label><br>
                        <textarea name="cluster_reamrks" id="cluster_reamrks" class="form-control" required></textarea>
                        <p id="clusterCityCount">Selected: 0</p>
                      </div>
                      <hr>
                      <center>
                        <button type="submit" class="btn" style="background-color: navy;color: white!important;" >Request For Approval</button>
                      </center>
                      <hr>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-6">
            <hr>
              <div class="text-center">
                 <a class="p-2" style="background-color: navy; color: white; font-weight: 600;" href="<?=base_url()?>Menu/TravelClusterDetails">Travel Cluster Details</a>
              </div>
            <hr>
                <div class="imag-box" style="align-items: center;justify-content: center;display: flex;">
                    <img src="<?=base_url()?>assets/image/gradient-world-tourism-day-illustration_52683-129641.avif" style="max-width: 70%;" class="img-fluid" alt="">
                </div>
              <div class="travel-cluster-quotes" style="font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; border-radius: 10px;">
                        <h2 style="color: #2c3e50;">✨ Plan New Travel Cluster</h2>
                        <ul style="list-style-type: none; padding-left: 0;">
                            <li style="margin-bottom: 15px;">
                                <blockquote style="border-left: 4px solid #3498db; padding-left: 15px; color: #34495e;">
                                    A new journey always begins with a bold plan and a curious heart.
                                </blockquote>
                            </li>
                            <li style="margin-bottom: 15px;">
                                <blockquote style="border-left: 4px solid #16a085; padding-left: 15px; color: #34495e;">
                                    Every travel cluster holds the potential for new stories, discoveries, and impact.
                                </blockquote>
                            </li>
                            <li>
                                <blockquote style="border-left: 4px solid #f39c12; padding-left: 15px; color: #34495e;">
                                    Don’t just travel—build meaningful paths that connect people and places.
                                </blockquote>
                            </li>
                        </ul>
                    </div>

              
            </div>
          </div>
<hr>
<br>
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




  $('#clusterState').on('change', function() {
    var clusterState = $('#clusterState').val();
    $.ajax({
      url: '<?=base_url();?>Menu/getAllclusterDistrict',
      type: "POST",
      data: {
        clusterState: clusterState
      },
      cache: false,
      success: function(result) {
        $("#clusterDistrict").html(result);
      }
    });
  });

  $('#clusterDistrict').on('change', function() {
    var clusterDistrict = $('#clusterDistrict').val();
    $.ajax({
      url: '<?=base_url();?>Menu/getAllclusterDistrictCity',
      type: "POST",
      data: {
        clusterDistrict: clusterDistrict
      },
      cache: false,
      success: function(result) {
        $("#clusterCity").html(result);
      }
    });
  });

function countSelected(selectID,messageID) {

        var selectElement = document.getElementById(selectID);
        var selectedCount = selectElement.selectedOptions.length;
        document.getElementById(messageID).textContent = 'Selected: ' + selectedCount;
}

  $("#example1").DataTable({
    "responsive": false, "lengthChange": false, "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

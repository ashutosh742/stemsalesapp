<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Travel Cluster | STEM APP | WebAPP</title>
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
              <h3 class="m-0 premium-color-1 text-center p-4" style="color: #ff009b;font-family: sans-serif;font-weight: 600;">Edit Travel Cluster</h3>
              <center>
                <p style="width: 70%;color: #001780;">The "Edit Travel Cluster" feature allows users to modify and customize their travel plans efficiently. Users can adjust destinations, update travel dates, and manage accommodations with ease. This tool provides flexibility and convenience, ensuring that every aspect of the travel itinerary meets the user's preferences and needs, enhancing the overall travel experience.</p>
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
             <?php if ($this->session->flashdata('error_message')): ?>
            <div class="container-fluid">
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
            <?php endif; ?>
            <?php 

              $clustername        = $clusterDatas[0]->clustername;
              $in_state_id        = $clusterDatas[0]->in_state;
              $in_district_id     = $clusterDatas[0]->in_district;
              $in_city_id         = $clusterDatas[0]->in_city;
              $travelType         = $clusterDatas[0]->travelType;
              $status             = $clusterDatas[0]->status;
              $apr_status         = $clusterDatas[0]->apr_status;
              $apr_by             = $clusterDatas[0]->apr_by;
              $apr_date           = $clusterDatas[0]->apr_date;
              $created_at         = $clusterDatas[0]->created_at;
              $updated_at         = $clusterDatas[0]->updated_at;



              $clusterEditAppprovedRequest  = sizeof($editRequestData);
              
              $request_remarks              = $editRequestData[0]->remarks;
              $request_apr_status           = $editRequestData[0]->apr_status;
              $request_apr_by               = $editRequestData[0]->apr_by;
              $request_apr_date             = $editRequestData[0]->apr_date;
              $request_created_at           = $editRequestData[0]->created_at;
              $request_updated_at           = $editRequestData[0]->updated_at;
              $request_apr_request_username = $editRequestData[0]->request_username;
              $request_apr_by_name          = $editRequestData[0]->apr_by_name;

              // echo "request_apr_status = ".$request_apr_status;

              // dd($editRequestData);
              // $request_apr_status = 0;
              // $request_apr_status = 1;
              ?>
            <div class="row p-3">
              <div class="col-md-6 col-lg-6">
                <div class="card card-primary card-outline" style="background: linear-gradient(to right, #fbffb2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;height: 100vh;" >
                  <div class="card-body box-profile">

                    <?php if($request_apr_status == 1){ ?> 
                    <form action="<?=base_url();?>Menu/UpdateUserCluster" method="post" enctype="multipart/form-data">
                      <div class="was-validated">
                        <input type="hidden" name="uid" value="<?=$uid?>">
                        <input type="hidden" name="travelId" value="<?=$travelId?>">
                        <div class="form-group">
                          <label for="task_type">Type of Travel</label><br>
                          <select class="form-control" name="typeofTravel" id="typeofTravel" required>
                            <option value="" Selected disabled>Select Location</option>
                            <option value="base" <?php echo ($travelType == 'base') ? 'selected' : ''; ?>>Base Location</option>
                            <option value="outStation" <?php echo ($travelType == 'outStation') ? 'selected' : ''; ?>>Out Station</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="task_type">Travel Cluster Name</label><br>
                          <input type="text" placeholder='Enter Cluster Name' class="form-control" value="<?=$clustername;?>" name="cluster" id="cluster" required>
                        </div>
                        <div class="form-group">
                          <label for="task_type">Select Cluster State</label><br>
                          <select name="clusterState" id="clusterState" class="form-control" required onchange="countSelected('clusterState','selectedCount')">
                            <option value='' disabled selected>Select State</option>
                            <?php foreach($state as $stat){?>
                            <option value='<?=$stat->state_id ?>' <?php echo ($in_state_id == $stat->state_id) ? 'selected' : ''; ?> ><?=$stat->state_title ?></option>
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
                          <select style="height: 400px;" name="clusterCity[]" multiple id="clusterCity" class="form-control" required onchange="countSelected('clusterCity','clusterCityCount')">
                          </select>
                          <p id="clusterCityCount">Selected: 0</p>
                        </div>
                        <hr>
                         <div class="form-group">
                        <label for="task_type">Remarks</label><br>
                        <textarea name="cluster_reamrks" id="cluster_reamrks" class="form-control" required></textarea>
                        <p id="clusterCityCount">Selected: 0</p>
                      </div>
                      <hr>
                        <center>
                          <button type="submit" class="btn" style="background-color: navy;color: white!important;">Request For Approval</button>
                        </center>
                        <hr>
                      </div>
                    </form>
                   
                    <?php }else{ ?>

                          <div class="text-center">
                            <h4>Travel Cluster Edit Request</h4>
                          </div>
                        
                        <?php if($clusterEditAppprovedRequest == 0){ ?> 
                          <div class="form-body">
                            <br>
                            <br>
                           
                            <form action="<?=base_url();?>Menu/CreateTravelClusterEditRequest" method="post" enctype="multipart/form-data">
                          <div class="was-validated">
                            <input type="hidden" name="uid" value="<?=$uid?>">
                            <input type="hidden" name="clustername" value="<?=$clustername?>">
                            <input type="hidden" name="travelId" value="<?=$travelId?>">
                            <input type="hidden" name="in_state" value="<?=$in_state_id?>">
                            <input type="hidden" name="in_district" value="<?=$in_district_id?>">
                            <input type="hidden" name="in_city" value="<?=$in_city_id?>">
                            <input type="hidden" name="travelType" value="<?=$travelType?>">

                            <div class="form-group">
                              <label for="task_type">Remarks</label><br>
                              <textarea style="height: 310px;" required="" name="permission_request" class="form-control" id="permission_request"></textarea>
                            </div>
                            <hr>
                            <center>
                              <button type="submit" class="btn" style="background-color: navy;color: white!important;">Request For Approval</button>
                            </center>
                            <hr>
                          </div>
                      </form>
                          </div>
                      

                      
                  <?php }else{ ?> 


                      <div style="background: linear-gradient(to right, #a7c9b9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 12px;">
                           
                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Request User Name:</strong>
                              <span style="color:#2c3e50;"><?= $request_apr_request_username ?></span>
                            </div>     
                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Cluster Name:</strong>
                              <span style="color:#2c3e50;"><?= $clustername ?></span>
                            </div>

                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Request Remarks:</strong>
                              <span style="color:#2c3e50;"><?= $request_remarks ?></span>
                            </div>

                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Approval Status:</strong>
                              <span style="color:#2c3e50;"><?php 
                              if($request_apr_status == 0){
                                echo "<span class='p-1 bg-warning'>Pending</span>";
                              }else if($request_apr_status == 1){
                                echo "<span class='p-1 bg-success'>Approved</span>";
                              }else if($request_apr_status == 2){
                                echo "<span class='p-1 bg-danger'>Reject</span>";
                              }
                              ?></span>
                            </div>

                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Approved By:</strong>
                              <span style="color:#2c3e50;"><?php 
                              if($request_apr_status == 0){
                                echo "<span class='p-1 bg-warning'>Pending</span>";
                              }else if($request_apr_status == 1 || $request_apr_status == 2){
                                echo $request_apr_by_name;
                              }
                               ?></span>
                            </div>

                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Approval Date:</strong>
                              <span style="color:#2c3e50;"><?php 
                              
                              if($request_apr_status == 0){
                                echo "<span class='p-1 bg-warning'>Pending</span>";
                              }else if($request_apr_status == 1 || $request_apr_status == 2){
                                echo $request_apr_date;
                              }
                              ?></span>
                            </div>

                            <div style="margin-bottom:15px; padding:10px 15px; border-left:4px solid #3498db; background-color:#f9fcff; border-radius:6px;">
                              <strong style="display:inline-block; width:180px; color:#34495e;">Request At:</strong>
                              <span style="color:#2c3e50;"><?= $request_created_at ?></span>
                            </div>

                           
                          </div>

                    <?php }?>

                      

                   <?php } ?>


                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-6">
                <hr>
                <div class="text-center">
                  <a class="p-2" style="background-color: navy; color: white; font-weight: 600;" href="<?=base_url()?>Menu/CreateTravelCluster">Create Travel Cluster</a>
                  <a class="p-2" style="background-color: navy; color: white; font-weight: 600;" href="<?=base_url()?>Menu/TravelClusterDetails">Our Travel Cluster Details
                  </a>
                </div>
                <hr>
                <div class="imag-box" style="align-items: center;justify-content: center;display: flex;">
                  <img src="<?=base_url()?>assets/image/gradient-world-tourism-day-background_52683-129652.avif" class="img-fluid" alt="">
                </div>
                <div class="quotes-container" style="background: #f5f9ff; border-radius: 10px; padding: 20px; font-family: Arial, sans-serif; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                  <h2 style="text-align: center; color: #2b5dab;">✈️ Change Travel Cluster Quotes</h2>
                  <ul style="list-style-type: none; padding-left: 0; color: #333;">
                    <li>🧭 <em>A shift in the route often leads to new discoveries.</em></li>
                    <li>🔁 <em>Changing clusters, not just directions—changing possibilities.</em></li>
                    <li>🌍 <em>New paths bring new perspectives. Update your journey.</em></li>
                    <li>🚀 <em>Travel smart. Change your cluster. Discover more.</em></li>
                    <li>🗺️ <em>A better route begins with a better plan. Rethink your cluster.</em></li>
                    <li>👣 <em>Every journey starts with a choice. Choose your next travel cluster wisely.</em></li>
                    <li>🎯 <em>Progress is not in motion—but in the right direction.</em></li>
                    <li>🌀 <em>Change your cluster. Reimagine your purpose.</em></li>
                    <li>✈️ <em>The destination may stay the same, but the cluster can transform the experience.</em></li>
                    <li>⚙️ <em>Don’t follow the path—redesign it. Change your travel cluster.</em></li>
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
      $( document ).ready(function() {
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
      
              var DistrictvaluesToSelect = [<?=$in_district_id;?>];
              // Iterate over each value and set the corresponding option as selected
              DistrictvaluesToSelect.forEach(function(value) {
                  $('#clusterDistrict option[value="' + value + '"]').prop('selected', true);
              });
              // Trigger the onchange event if needed
              $('#clusterDistrict').trigger('change');
      
                
                  $.ajax({
                  url: '<?=base_url();?>Menu/getAllclusterDistrictCity',
                  type: "POST",
                  data: {
                      clusterDistrict: DistrictvaluesToSelect
                  },
                  cache: false,
                  success: function(result) {
                      $("#clusterCity").html(result);
      
                      var clusterCityvaluesToSelect = [<?=$in_city_id;?>];
                      // Iterate over each value and set the corresponding option as selected
                      clusterCityvaluesToSelect.forEach(function(value) {
                          $('#clusterCity option[value="' + value + '"]').prop('selected', true);
                      });
                      // Trigger the onchange event if needed
                      $('#clusterCity').trigger('change');
                  }
                  });
            }
          });
    
       function countSelected(selectID,messageID) {
              var selectElement = document.getElementById(selectID);
              var selectedCount = selectElement.selectedOptions.length;
              document.getElementById(messageID).textContent = 'Selected: ' + selectedCount;
      }
      
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
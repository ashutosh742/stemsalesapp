<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Travel Cluster Details | Stem APP | WebAPP</title>
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
   
     .scrollme {
        overflow-x: auto;
      }
  
      .text-dark {
        color: black !important;
      }
      .form-container {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .form-container input, .form-container button {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
      }
      .form-container button {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
      }
      .col-sm-6.text-right-data {
        align-items: right;
        justify-content: right;
        display: flex;
      }
      .row-equal-height {
        display: flex;
        flex-wrap: wrap;
      }
      .row-equal-height > [class*='col-'] {
        display: flex;
        flex-direction: column;
      }
      .card {
        margin-bottom: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
      }
      .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center; /* Center content vertically */
      }

    tr{
      font-weight: bold;
    }
    .beautiful-textarea {
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            font-size: 16px;
            resize: none; /* Prevents resizing */
            outline: none; /* Removes the default focus outline */
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .beautiful-textarea:focus {
            border-color: #66afe9;
            box-shadow: 0 4px 12px rgba(102, 175, 233, 0.4);
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
        <br>
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
        <div class="frame-layer-3" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <h3 class="m-0 premium-color-1 text-center p-4" style="color: #ff009b;font-family: sans-serif;"> Travel Cluster Edit Request</h3>
            <center>
                <p style="width: 70%;">Travel Cluster Edit Request allows users to request changes to existing travel cluster plans. This feature ensures that any modifications—such as changes in location, date, or travel type—are properly documented and sent for approval. It helps maintain accuracy, transparency, and accountability in travel planning workflows across teams.</p>
            </center>
        <p style="color:navy; font-weight:bold;" class='text-center'><?=$cdate ?></p>
        </div>
          
          <?php  
         

            // dd($editRequestData);
          ?>
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

      
          <div class="table-responsive">
            <div class="table-responsive text-nowrap">
              <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="thead-dark" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                  <tr>
                    <th>ID</th>
                    <th>Request User Name</th>
                    
                    <th>Base / Out Station</th>
                    <th>Cluster Name</th>
                    <th>Cluster State</th>
                    <th>Cluster District</th>
                    <th>Cluster City</th>
                    <th>Created Date</th>

                    <th>Approved Status</th>
                    <th>Approved BY</th>
                    <th>Approved Date</th>
                    <th>Approved Remarks</th>
                    <th>Request Remarks</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $k=1;
                    foreach($editRequestData as $c){
                      if(is_null($c->request_id)){
                        continue;
                      }
                      
                    $r = rand(150, 255);
                    $g = rand(150, 255);
                    $b = rand(150, 255);
                    $backgroundColor = "rgba($r, $g, $b,.2)";

                    $hue        = rand(0, 360); // Full color wheel
                    $saturation = rand(60, 100); // High saturation for rich colors
                    $lightness  = rand(30, 45); // Low lightness for a deeper tone
                    $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                  ?>
                   <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                    <td><?= $k ?></td>
                   <td><?php 
                    if(!is_null($c->travelType)){
                        echo $c->travelType;
                    }else{
                        echo "NA";
                    }
                    ?></td>
                    
                    <td><?= $c->request_username  ?></td>
                    
                    <td><?= $c->clustername  ?></td>
                    <td><?= $c->state_title ?></td>
                    <td><?= $c->district_titles;  ?></td>
                    <td><?= $c->city_titles;  ?></td>
                    <td><?= $c->created_at;?></td>
                   
                    <td><?php 
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1){
                        echo "<span class='p-1 bg-success'> Approved </span>";
                    }else if($c->apr_status == 2){
                        echo "<span class='p-1 bg-danger'> Rejected </span>";
                    }
                    ?> </td>
                    <td><?php 
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_by_name;
                    }
                    ?> </td>
                    <td><?php
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_date;
                    }
                   ?></td>
                    <td><?php
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_remarks;
                    }
                   ?></td>
                        <td><?= $c->remarks  ?></td>
                    <td>
                        <?php if($c->apr_status == 0){ ?> 
                       <span class="p-1 bg-success btn" onclick="TakeRequestAction(<?= $c->request_id ?>,'Approved')" style="font-weight: 600;"> Approve </span> &nbsp;&nbsp;
                       <span class="p-1 bg-danger btn" onclick="TakeRequestAction(<?= $c->request_id ?>,'Reject')" style="font-weight: 600;"> Reject </span>
                    <?php  }else{

                            if($c->apr_status == 1){
                                echo "<span class='p-1 bg-success'> Approved </span>";
                            }else if($c->apr_status == 2){
                                echo "<span class='p-1 bg-danger'> Rejected </span>";
                            }
                    }  ?>
                    
                    </td>

                  </tr>
                  <?php $k++; } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <hr>
        <br>



         <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Add Remark</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                   <form action="<?=base_url();?>Menu/ActionTravelClusterEditRequest" method="post" >
                                        <input type="hidden" id="request_id" value="" name="request_id">
                                        <input type="hidden" id="request_type" value="" name="request_type">
                                         <div class="mb-3 mt-3">
                                          <textarea id="request_remarks" name="request_remarks" cols="30" placeholder="Add Remark " class="form-control"  rows="4" required></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" id="requestSubmit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>

      </section>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
      <script type='text/javascript'>
            function TakeRequestAction(id,val){
                $('#exampleModalCenterdata').modal('show');
                $('#request_id').val(id);
                $('#request_type').val(val);
                if(val == 'Approved'){
                    $("#requestSubmit").text("Approved").css("background-color","green");
                }
                if(val == 'Reject'){
                    $("#requestSubmit").text("Reject").css("background-color","red");;
                }
            }
      </script>
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
      "buttons": ["csv", "excel", "pdf"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
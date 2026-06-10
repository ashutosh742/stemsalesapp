<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todays Pending Meetings Task Request | STEM APP| WebAPP</title>
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
      .ApprovedStatus{
      padding:6px 10px;
      border-radius:4px;
      }
      .ApprovedStatus.Pending{
      background:orange;
      color:white;
      }
      .ApprovedStatus.Reject{
      background:red;
      color:white;
      }
      .ApprovedStatus.approved{
      background:green;
      color:white;
      }
      .colapsboxsha{
      border-radius:22px;
      box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;
      }
      .modal.fade.show{
      background: rgba(100, 200, 150, 0.4);
      }
      #plandate{
      width:300px;
      }
      .setpaldate{
      display:flex;
      }
      tr,td{
        font-weight: 600;
      }
      form#add_target_form {
    background: beige;
    padding: 20px;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php 
      $this->load->view($dep_name.'/nav.php');
      ?>
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
        <section class="content">
          <div class="container-fluid">
            <?php //dd($getreqData); ?>
            <div class="row p-3">
              <div class="col-sm col-md-12 col-lg-12 m-auto">
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
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    <div class="text-center p-2 text-white" style="background-color: #ff0076 !important;">
                      <h4><i>Todays Pending Meetings Task Request</i></h4>
                      <h6><?=$tdate;?></h6>
                    </div>
                    <hr>
        
                  

                    <div class="table-responsive text-nowrap">
                      <table id="exampledatRequest" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                        <tr>
                          <th scope="col">#️⃣</th>
                          <th class="text-capitalize">👤 Task User Name</th>
                          <th class="text-capitalize">📝 Number of Tasks</th>
                          <th class="text-capitalize">📅 Planner Request Date</th>
                          <th class="text-capitalize">💬 Request Message</th>
                          <th class="text-capitalize">✅ Approved Status</th>
                          <th class="text-capitalize">🗓️ Approved Date</th>
                          <th class="text-capitalize">🧑‍💼 Approved By</th>
                          <th class="text-capitalize">🕒 Created Date</th>
                          <th class="text-capitalize">⚙️ Action</th>
                        </tr>
                        </thead>
                        <tbody>
                          <?php
                            $k =1;
                            foreach($getPMTDRequests as $getPMTDRequest){
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.2)";

                            $hue = rand(0, 360); // Full color wheel
                            $saturation = rand(60, 100); // High saturation for rich colors
                            $lightness = rand(30, 45); // Low lightness for a deeper tone
                            
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                              ?>
                           <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$getPMTDRequest->id?>">
                            <th><?= $k ?></th>
                            <td class="text-capitalize"><?= $getPMTDRequest->request_user_name ?></td>
                            <td><?= $getPMTDRequest->request_task_count ?></td>
                            <td><?= $getPMTDRequest->request_date ?></td>
                            <td>
                                                    
                             
                               <?php 
                              $getTaskReqLists = $this->Menu_model->GetTblCallEventsTasksList($getPMTDRequest->task_ids);
                              $l = 0;
                              $m=1;
                              $requestRemarksarrays = explode(",", $getPMTDRequest->remarks);
                              foreach ($getTaskReqLists as $value){ ?>
                              <div class="userrequestbg" style="background: azure;padding: 5px;margin-bottom: 2px;">
                                <span style="color: navy;"> <b> #<?=$m;?> - CID:</b>  <?= $value->cid?> </span> |
                                <span style="color: #22850e;"> <b>Company Name</b>:  <?= $value->company_name?> </span> |
                                <span style="color:rgb(18, 14, 133);"> <b>Appointment Datetime</b>:  <?= $value->appointmentdatetime?> </span> |
                                <span style="color: #eb2727;"> <b>Request Reamrks:</b>  <?= $requestRemarksarrays[$l]; ?> </span> | 
                                <span style="color: #eb2727;"> <b>Delete Status:</b>  <?php 
                                if($value->delete_request > 0){
                                echo "<span class='bg-success p-1'>Deleted</span>";
                                }else{
                                  echo "<span class='bg-warning p-1'>Pending</span>";
                                }
                                ?> </span>  | 
                                <span style="color:rgb(32, 4, 4);"> <b>Delete Remarks:</b>  <?php 
                                if($value->delete_request > 0){
                                echo $value->delete_remarks;
                                }else{
                                  echo "<span class='bg-warning p-1'>Pending</span>";
                                }
                                ?> </span> 
                                </div>
                                <hr>
                              <?php $m++; $l++; } ?>
                             


                              </td>

                            <td><?php 
                            if($getPMTDRequest->apr_status == 1){
                                echo "<span class='bg-success p-1'>Approved</span>";
                            }else if($getPMTDRequest->apr_status == 0){
                                echo "<span class='bg-warning p-1'>Pending</span>";
                            }
                            ?></td>
                            <td><?php 
                            if($getPMTDRequest->apr_date == ''){
                                echo "<span class='bg-warning p-1'>Pending</span>";
                            }else if($getPMTDRequest->apr_date !== ''){
                                echo $getPMTDRequest->apr_date;
                            }
                            ?></td>
                            <td><?php 
                            if($getPMTDRequest->apr_by_name == ''){
                                echo "<span class='bg-warning p-1'>Pending</span>";
                            }else if($getPMTDRequest->apr_by_name !== ''){
                                echo $getPMTDRequest->apr_by_name;
                            }
                            ?></td>
                             <td><?= $getPMTDRequest->created_at ?></td>

                              <td><?php 
                            if($getPMTDRequest->apr_status == 1){
                                echo "<span class='bg-success p-1'>Approved</span>";
                            }else if($getPMTDRequest->apr_status == 0){ ?>
                               <p><button type="button" class="btn btn-primary"  onclick="Approved(<?= $k ?>,<?= $getPMTDRequest->id ?>,'Approved')">Approved</button></p>
                           
                           <?php }
                            ?></td>

                          
                              
                          </tr>
                          <?php $k++; } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <hr>
 <br>
 <br>   <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Approve Remark</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                   <form action="<?=base_url();?>Menu/PendingMeetingsTaskRequestApproved" method="post" >
                                        <input type="hidden" id="approveid" value="" name="approveid">
                                         <div class="mb-3 mt-3">
                                          <textarea id="approve_remarks" name="approve_remarks" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>
 <br>

        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type='text/javascript'>
      
          function Approved(mid,id,val){
          $('#exampleModalCenterdata').modal('show');
          $('#approveid').val(id);
          }
        </script>
        <!-- /.row (main row) -->
      </div>
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
      // $("#exampledata").DataTable({
      // "responsive": false, "lengthChange": false, "autoWidth": false,
      // "buttons": ["copy", "csv", "excel", "pdf", "print"]
      // }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
      
    </script>
  </body>
</html>
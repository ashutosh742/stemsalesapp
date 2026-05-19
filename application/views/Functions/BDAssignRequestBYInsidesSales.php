<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BD Assign Request BY Insides Sales | STEM APP| WebAPP</title>
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
    
        <!-- /.content-header -->
        <section class="content">
          <div class="container-fluid">
            
            <div class="row p-3">
              <div class="col-sm col-md-12 col-lg-12 m-auto">
                <?php if ($this->session->flashdata('success_message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <?php endif; ?>
                <?php if ($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <?php endif; ?>


                <?php 
                //  dd($getreqData);
                ?>


                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    <div class="text-center mt-2 mb-2" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h3><i>BD Assign Request BY Insides Sales</i></h3>
                    </div>
                   

                    <hr>
                    <br>
                     <div class="table-responsive text-nowrap">
                            <table id="exampledata" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead class="thead-dark">
                               <tr>
                                    <th scope="col">#️⃣</th>
                                    <th scope="col">📅 Request Date</th>
                                    <th scope="col">📅 Need To Assign BD</th>
                                    <th scope="col">👤 Request Inside Sales</th>
                                    <th scope="col">🤝 Main BD</th>
                                    <th scope="col">🤝 Inside Sale (Company Mapped)</th>
                                    <th scope="col">💬 Request Message</th>

                                    <th scope="col">🏢 Company Name</th>
                                    <th scope="col">🏢 Company CID</th>
                                    <th scope="col">📊 Current Status</th>
                                    <th scope="col">📊 Travel Cluster</th>
                                    <th scope="col">🗓️ Meeting Date & Time</th>
                                    <th scope="col">💬 Meeting Type</th>

                                    <th scope="col">✅ Approval Status</th>
                                    <th scope="col">👍 Approve / 👎 Reject</th>
                                    <th scope="col">👍 Assign message</th>
                                    <th scope="col">📅 Assign Date</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $j =1;
                                foreach($getreqData as $data){ ?>
                                <tr>
                                  <th><?= $j ?></th>
                                  <td><?= $data->created_at ?></td>
                                  <td><?= $data->need_to_assign_bd_message ?></td>
                                  <td><?= $data->request_by_user ?></td>
                                  <td><?= $data->mainbd_name ?></td>
                                  <td><?= $data->insidebd_name ?></td>
                                  
                                  <td><?=$data->remarks ?></td>
                                  <td>
                                    <a target="_blank" href="<?=base_url()?>Menu/CompanyDetails/<?=$data->cid ?>">
                                        <?= $data->compname ?>
                                    </a>
                                  </td>
                                  <td><a target="_blank" href="<?=base_url()?>Menu/CompanyDetails/<?=$data->cid ?>"><?=$data->cid ?></a></td>
                                  <td><?= $data->company_cstatus ?></td>
                                  <td><a target="_blank" href="<?=base_url()?>Menu/TravelClusterDetailsByID/<?=$data->cluster_id ?>"><?= $data->clustername ?></a></td>
                                  <td><?= $data->meeting_date_time ?></td>
                                  <td><?= $data->types_of_meetings ?></td>
                                  <td>
                                    <?php
                                    if($data->assign_status == 0){ ?>
                                    <span class="p-1 bg-warning mr-2">Pending</span>
                                    <?php }else if($data->assign_status == 1){ ?>
                                    <span class="p-1 bg-success mr-2">Assigned</span>
                                    <?php }else if($data->assign_status == 2){ ?>
                                    <span class="p-1 bg-danger mr-2">Rejected</span>
                                    <?php }?>
                                  </td>
                                  <td>
                                    <?php if($data->assign_status == 0){ ?>
                                        <div>
                                            <button type="button" class="btn btn-success"  onclick="AssignedBDMapping(<?= $data->id?>,'Approved')">Approved</button>
                                            <button type="button" class="btn btn-danger"  onclick="AssignedBDMapping(<?= $data->id?>,'Reject')">Reject</button>
                                        </div>
                                    <?php }else if($data->assign_status == 1){ ?>
                                    <span class="p-1 bg-success mr-2">Assigned</span>
                                    <?php }else if($data->assign_status == 2){ ?>
                                    <span class="p-1 bg-danger mr-2">Reject</span>
                                    <?php }?>
                                  </td>
                                  <td><?= $data->assign_message ?></td>
                                  <td><?= $data->assign_date ?></td>
                                </tr>
                                <?php $j++; } ?>
                              </tbody>
                            </table>
                          </div>


                   
                    
                    
                    <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                           <form action="<?=base_url();?>Menu/BDAssignRequestBYInsidesSalesSubmit" method="post" 
                                style="max-width:600px; margin:20px auto; padding:20px; border:1px solid #ddd; border-radius:12px; background:#f9f9f9; box-shadow:0 4px 8px rgba(0,0,0,0.1);">

                                <!-- Hidden Inputs -->
                                <input type="hidden" id="request_id" name="request_id" value="">
                                <input type="hidden" id="approved_type" name="approved_type" value="">

                               
                                <div id="meeting_date_time_card">
                        
                                      <div class="col-12 col-md-12 mb-3" id="need_to_assign_bd_card">
                                        <?php  
                                        
                                        // $clusterManagerDatas =  $this->Menu_model->getAllBDInClusterManagerUid($user['user_id']);
                                        
                                        // $clusterManagerDatas =  $this->Menu_model->GetClusterUserMappedinClusterMapID($user['user_id']);
                                        
                                        // echo "<pre>";
                                        // print_r($clusterManagerDatas);
                                        
                                        ?>



                                            <label for="need_to_assign_bd">Select Assign BD</label>
                                            <select type="text" class="form-control" name="need_to_assign_bd" id="need_to_assign_bd" >
                                            <option value="" selected disabled> -- Select --</option>
                                            
                                            </select>
                                        </div>

                                </div>

                                <!-- Remarks -->
                                <label for="manager_remarks" 
                                    style="display:block; font-weight:bold; margin-bottom:8px; font-size:15px;">
                                    📝 Manager Remarks
                                </label>
                                <textarea id="manager_remarks" name="manager_remarks" rows="4" required placeholder="Add your remarks here..."
                                        style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; resize:none; font-size:14px; margin-bottom:20px;"></textarea>

                                <!-- Submit -->
                                <div style="text-align:center;">
                                    <button type="submit" 
                                            style="background:#28a745; color:white; padding:10px 20px; font-size:15px; border:none; border-radius:8px; cursor:pointer; transition:0.3s;">
                                        ✅ Submit
                                    </button>
                                </div>
                            </form>

                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type='text/javascript'>
     
        
        function AssignedBDMapping(id,val){
            $("#meeting_date_time_card").hide();
             if(val == 'Approved'){
                $("#exampleModalLongTitle").text("Approved Remarks");
                  $("#meeting_date_time_card").show();
            }else{
                $("#exampleModalLongTitle").text("Rejected Remarks");
                 $("#meeting_date_time_card").hide();
            }


              $.ajax({
                url:'<?=base_url();?>Menu/GetClusterMapinTableRequestByISales',
                type: "POST",
                data: {
                request_id: id
                },
                cache: false,
                success: function a(result){
                  $("#need_to_assign_bd").html('');
                  $("#need_to_assign_bd").html(result);

                }
                });



        $('#exampleModalCenterdata').modal('show');
        $('#request_id').val(id);
        $('#approved_type').val(val);
        // $('#exampleModalCenterdata'+mid).modal('show');
        // $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
        }
        </script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
      <!-- </section> -->
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?= date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
  $("#exampledata").DataTable({
  "responsive": false, "lengthChange": false, "autoWidth": false,
  "buttons": ["copy", "csv", "excel", "pdf", "print"]
  }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
  
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Handover Details | STEM APP | WebAPP</title>
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
    <style>
      .scrollme {
      overflow-x: auto;
      }
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }


         .custom-btn {
            /* width: 130px;
            height: 40px; */
            color: #fff;
            border-radius: 5px;
            /* padding: 10px 25px; */
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
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
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
      
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
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
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">🏷️ Vendor Request Details </h3>
                    <hr style="width:25%;">
         
                 
                    <div class="table-responsive text-nowrap">

                         <table id="example1" class="table table-striped">
       <thead class="thead-dark">
            <tr>
                <th>🔢 Sr No.</th>
                <th>📌 Request Type</th>
                <th>🙍 Task Username</th>

                <th>🏢 Office ID</th>
                <th>📋 Task ID</th>

                <th>👥 Total Vendors</th>
                <th>⭐ Best Vendor ID</th>
                <th>⭐ Best Vendor Details</th>
                <th>💰 Best Vendor Price</th>
                <th>📄 Best Vendor Invoice File</th>
                <th>📝 Best Vendor Remarks</th>
                <th>📨 Request Remarks</th>

                <th>🛠️ Task Type</th>
                <th>📌 Task Name</th>
                <th>🏫 School ID</th>
                <th>🏫 School Name</th>

                <th>✅ AM Status</th>
                <th>📅 AM Approval Date</th>
                <th>👤 AM Approved By</th>
                <th>🗒️ AM Remarks</th>

                <th>🛡️ Admin Status</th>
                <th>📅 Admin Approval Date</th>
                <th>👤 Admin Approved By</th>
                <th>🗒️ Admin Remarks</th>



                <th>💳 UTR Number</th>
                <th>⏰ Created At</th>
            </tr>
        </thead>
        </thead>
        <tbody>
          <?php 
            $i = 1; 
            foreach($vendorRequestDatas as $row) {
              $r = rand(150, 255);
              $g = rand(150, 255);
              $b = rand(150, 255);
              $backgroundColor = "rgba($r, $g, $b,.2)";
            
              $hue        = rand(0, 360);  // Full color wheel
              $saturation = rand(60, 100); // High saturation for rich colors
              $lightness  = rand(30, 45);  // Low lightness for a deeper tone
              $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

              $vendor_name       = $row->vendor_name;
              $vendor_state      = $row->vendor_state;
              $vendor_district   = $row->vendor_district;
              $vendor_city       = $row->vendor_city;
              $contact_person    = $row->contact_person;
              $vendor_email      = $row->vendor_email;   // because of "vendors.email as vendor_email"
              $vendor_phone      = $row->vendor_phone;   // because of "vendors.phone as vendor_phone"
              $vendor_address    = $row->vendor_address; // because of "vendors.address as vendor_address"

            ?>
            <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($row->request_type) ?></td>
                <td><?= htmlspecialchars($row->task_username) ?></td>

                <td><?= htmlspecialchars($row->office_id) ?></td>
                <td> <a target="_BLANK" href="https://stemoppapp.in/Menu/TBLTaskDetails/<?= htmlspecialchars($row->task_id) ?>"><?= htmlspecialchars($row->task_id) ?></a></td>

                <td>
                  <a target="_BLANK" href="https://stemoppapp.in/Menu/AllRequestVendorDetails/<?= htmlspecialchars($row->buy_requests_id) ?>">
                    <?= htmlspecialchars($row->total_vendors_request) ?>
                  </a>  
                </td>
                <td><a target="_BLANK" href="https://stemoppapp.in/Menu/VendorDetails/<?= htmlspecialchars($row->is_best_vendor_id) ?>"><?= htmlspecialchars($row->is_best_vendor_id) ?></a></td>

                  <td>
                    <div class="card shadow" style="border-radius:15px; border:1px solid #e0e0e0; margin:15px;">
                        <div class="card-body" style="padding:25px; background:#f9fafc;text-align: left;background: honeydew;">

                            <h5 class="card-title text-center" style="font-weight:600; color:#2c3e50;">
                                🏢 Best Vendor Details
                            </h5>
                            <hr>

                            <p style="margin-bottom:8px;"><strong>🏷️ Name:</strong> <?= htmlspecialchars($vendor_name); ?></p>
                            <p style="margin-bottom:8px;"><strong>🌍 State:</strong> <?= htmlspecialchars($vendor_state); ?></p>
                            <p style="margin-bottom:8px;"><strong>📍 District:</strong> <?= htmlspecialchars($vendor_district); ?></p>
                            <p style="margin-bottom:8px;"><strong>🏙️ City:</strong> <?= htmlspecialchars($vendor_city); ?></p>
                            <p style="margin-bottom:8px;"><strong>👤 Contact Person:</strong> <?= htmlspecialchars($contact_person); ?></p>
                            <p style="margin-bottom:8px;"><strong>📧 Email:</strong> <?= htmlspecialchars($vendor_email); ?></p>
                            <p style="margin-bottom:8px;"><strong>📞 Phone:</strong> <?= htmlspecialchars($vendor_phone); ?></p>
                            <p style="margin-bottom:0;"><strong>🏠 Address:</strong> <?= htmlspecialchars($vendor_address); ?></p>

                        </div>
                    </div>
                </td>


                <td><?= htmlspecialchars($row->is_best_vendor_price) ?></td>
                <td><a target="_BLANK" href="https://stemoppapp.in/<?= htmlspecialchars($row->is_best_vendor_invoice_file_path) ?>">View</a></td>
                <td><?= htmlspecialchars($row->is_best_vendor_remarks) ?></td>
                <td><?= htmlspecialchars($row->request_remarks) ?></td>

                <td><?= htmlspecialchars($row->tasktype) ?></td>
                <td><?= htmlspecialchars($row->taskname) ?></td>
                
                <td>
                  <?php if(!empty($row->sid)){ ?> 
                  <a class="bg-success p-1 text-white" target="_blank" href="https://stemoppapp.in/Menu/SchoolProfileDetails/<?=$row->sid?>">SPD ID - <?=$row->sid?></a> 
                  <?php }else if(!empty($row->rsid)){ ?> 
                  <a class="bg-success p-1 text-white" target="_blank" href="https://stemoppapp.in/Menu/SchoolRequestProfileDetails/<?=$row->rsid?>">RSPD ID - <?=$row->rsid?></a> 
                  <?php }else{ ?>
                  - 
                  <?php } ?>
                </td>

                <td><?= htmlspecialchars($row->sname) ?></td>

                <td> 
                    <?php if(empty($row->am_approved_status)){ ?> 
                              <span class='bg-warning p-1 text-white'>Pending BY Manager</span>
                     <?php }else{ 
                        if($row->am_approved_status == 1){
                            echo "<span class='bg-success p-1 text-white'>Manager Approved</span>";
                        }else if($row->am_approved_status == 2){
                            echo "<span class='bg-danger p-1 text-white'>Manager Rejected</span>";
                        }
                    } ?>   
                </td>
                <td><?php if(empty($row->admin_manager_name)){echo "-";}else{echo $row->admin_manager_name;} ?></td>
                <td><?php if(empty($row->am_approved_date)){echo "-";}else{echo $row->am_approved_date;} ?></td>
                <td><?php if(empty($row->am_approved_remarks)){echo "-";}else{echo $row->am_approved_remarks;} ?></td>
                
                <td> 
                    <?php if($row->am_approved_status == 1){ ?>

                            <?php if(empty($row->admin_approved_status)){ ?> 
                            
                                <?php if(in_array($user['type_id'],[1,2])){?> 
                                    <button type="button" class="btn btn-warning"  onclick="ApprovelProcess(<?= $row->buy_requests_id?>,'Admin Approvel')">Manager Approvel</button>
                                <?php }else{ ?>
                                    <span class='bg-warning p-1 text-white'>Pending BY Manager</span>
                                <?php } ?>
                                     
                            <?php }else{ 
                                if($row->admin_approved_status == 1){
                                    echo "<span class='bg-success p-1 text-white'>Approved</span>";
                                }else if($row->admin_approved_status == 2){
                                    echo "<span class='bg-danger p-1 text-white'>Rejected BY Admin</span>";
                                }
                            } ?> 
                       <?php }else if($row->am_approved_status == 2){
                            echo "<span class='bg-danger p-1 text-white'>Rejected BY Manager</span>";
                        }else if(empty($row->am_approved_status)){
                            echo "<span class='bg-warning p-1 text-white'>Pending For Approvel BY Manager</span>";
                        }
                     ?>   
                </td>
               
                <td><?php if(empty($row->admin_approved_date)){echo "-";}else{echo $row->admin_approved_date;} ?></td>
                 <td><?php if(empty($row->admin_approved_by)){echo "-";}else{

                    $adminUDetails =  $this->Menu_model->get_userbyid($row->admin_approved_by);
                    if(sizeof($adminUDetails) > 0){
                        echo $adminUDetails[0]->name;
                    }else{
                        echo $row->admin_approved_by;
                    }

                    
                    } ?></td>
                <td><?php if(empty($row->admin_approved_remakrs)){echo "-";}else{echo $row->admin_approved_remakrs;} ?></td>
                <td> 
                    
                    <?php if($row->am_approved_status == 1){ ?>
                  
                        <?php 
                            if($row->admin_approved_status == 1){

                                 ?> 
                                 <?php if(empty($row->utr_number)){echo "<span class='bg-warning p-1 text-white'>UTR Pending</span>";}else{echo $row->utr_number;} ?>
                                 <?php 
                            }else if($row->admin_approved_status == 2){
                                echo "<span class='bg-danger p-1 text-white'>Rejected BY Admin</span>";
                            }else if(empty($row->admin_approved_status)){
                                 echo "<span class='bg-warning p-1 text-white'>Pending BY Admin Side</span>";
                            }
                        }else if($row->am_approved_status == 2){
                            echo "<span class='bg-danger p-1 text-white'>Request Rejected BY Manager</span>";
                        }else if(empty($row->am_approved_status)){
                            echo "<span class='bg-warning p-1 text-white'>Pending For Approvel BY Manager</span>";
                        }
                     ?>   
                </td>
                <td><?= htmlspecialchars($row->created_at) ?></td>
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
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>


       <?php if(in_array($user['type_id'],[1,2])){?> 
                  <div class="modal fade" id="AssignYourselfCenterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header" style="background-color: #d1e7ff;">
                            <h5 class="modal-title text-center" style="color: red;" id="exampleModalLongTitle"><b><u>Approvel Process</u></b> </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" style="background-color: #ffc4c45c;">
                        

                            <form action="<?=base_url();?>Menu/VendorRequestApprove" method="post">
                            <input type="hidden" id="request_id" name="request_id" value="" class="form-control" required />
                            <div class="mb-4 form-group">
                                <label for="defaultSelect" class="form-label">Select Approved / Reject</label>
                                <select id="defaultSelect" name="request_status" class="form-control" required>
                                    <option value="">select</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Reject</option>
                                </select>
                            </div>
                            <div>
                            <label for="exampleFormControlTextarea1" class="form-label">Remarks</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" name="remarks" required rows="3"></textarea>
                            </div>
                            <hr>
                            <div class="form-group text-center">
                            <button type="submit" class="btn btn-success mt-2" id="plannerRequestbtn">Submit</button>
                            </div>
                        </form>

                          </div>
                        </div>
                      </div>
                    </div>
          <?php } ?>





    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     <script>
              function ApprovelProcess(id){
                $('#AssignYourselfCenterModal').modal('show');
                $('#request_id').val(id);
            }


</script>

    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
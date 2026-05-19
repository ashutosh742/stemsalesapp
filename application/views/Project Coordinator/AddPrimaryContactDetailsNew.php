<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Add Primary Contact Details | STEM APP | WebAPP</title>
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
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php require('nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
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
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header text-center">
                    <h3 class="text-center m-3 text-secondary">Add Contact Details</h3>
                    <hr>
                    <h5 class="text-uppercase"><?=$cmData[0]->compname;?></h5>
                  </div>
                  <?php // dd($cmData[0]->id);
                   $requestDatacnt = sizeof($requestData);
                   if($requestDatacnt > 0){
                    $request_status = $requestData[0]->status;
                   }else{
                    $request_status = '';
                   }
                  
                  $addrequest = $requestDatacnt;
                  ?>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="container body-content">
                      <?php  $ccmDatacnt = sizeof($ccmData);?>
                      <div class="formcard">

                        <?php if($addrequest > 0 && $request_status == 1){ ?>
                        <form method="post" action="<?=base_url().'Menu/AddOrUpdateCompanyContactDtails'?>">
                        <input type="hidden" class="form-control" id="cid" name="company_id" value="<?=$cmData[0]->id;?>">
                          <div id="contactDetailsContainer">
                            <?php
                            if($ccmDatacnt > 0){ 
                            foreach($ccmData as $cData){ ?>
                             <input type="hidden" class="form-control" name="ccid[]" value="<?=$cData->id;?>" readonly>
                            <div class="card contactDetails" id="contactDetails">
                              <div class="form-row">
                                <div class="form-group col-md-12 text-right">
                                <button type="button" class="btn btn-success addContact">+</button>&nbsp;
                                <button type="button" class="btn btn-danger removeContact">-</button>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="contactperson">Contact Person</label>
                                  <input type="text" class="form-control" value="<?=$cData->contactperson;?>" name="contactperson[]" placeholder="Name" required>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="inputEmail4">Email</label>
                                  <input type="email" class="form-control" name="emailid[]" value="<?=$cData->emailid;?>" placeholder="Email" required>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="phoneno">Phone No</label>
                                  <input type="text" class="form-control" name="phoneno[]" value="<?=$cData->phoneno;?>" placeholder="Phone Number"  title="Please enter a valid Indian phone number starting with 7, 8, or 9 and followed by 10 digits." required>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="Designation">Designation</label>
                                  <input type="text" class="form-control" value="<?=$cData->designation;?>" name="designation[]" placeholder="Designation" required>
                                </div>
                                <div class="form-group col-md-12">
                                  <label for="inputState">Type</label>
                                  <select name="type[]" class="form-control" required>
                                    <?php 
                                    $ctype = $cData->type;
                                    if($ctype == 'primary'){
                                            $primaryslct = 'selected';
                                    }else if($ctype == 'alternate'){
                                            $alternateslct = 'selected';
                                    }
                                    ?>
                                    <option <?=$primaryslct;?> value="primary">Primary</option>
                                    <option <?=$alternateslct;?> value="alternate">Alternate</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <?php }}else{ ?>
                                <div class="text-center">
                                <span class="text-danger p-2"><strong>* No any contact details are available in this company. Please provide contact information.</strong></span>
                                </div>
                                <hr>
                                <div class="card contactDetails" id="contactDetails">
                              <div class="form-row">
                                <div class="form-group col-md-12 text-right">
                                <button type="button" class="btn btn-success addContact">+</button>&nbsp;
                                <button type="button" class="btn btn-danger removeContact">-</button>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="contactperson">Contact Person</label>
                                  <input type="text" class="form-control" name="contactperson[]" placeholder="Name" required>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="inputEmail4">Email</label>
                                  <input type="email" class="form-control" name="emailid[]" placeholder="Email" required>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="phoneno">Phone No</label>
                                  <input type="text" class="form-control" name="phoneno[]" placeholder="Phone Number" title="Please enter a valid Indian phone number starting with 7, 8, or 9 and followed by 10 digits." required>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="Designation">Designation</label>
                                  <input type="text" class="form-control" name="designation[]" placeholder="Designation" required>
                                </div>
                                <div class="form-group col-md-12">
                                  <label for="type">Type</label>
                                  <select name="type[]" id="type" class="form-control" required>
                                    <option value="primary">Primary</option>
                                    <option value="alternate">Alternate</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <?php } ?>
                          </div>
                         <center> <button type="submit" class="btn btn-primary">Submit</button></center>
                        </form>
                        <?php }else{ ?>
                           <div class="requestform">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php 
                                    if($requestDatacnt == 0){
                                    ?>
                                <form method="post" action="<?=base_url().'Menu/CreatePermissionRequest'?>">
                                    <div class="form-group col-md-12">
                                        <input type="hidden" name="cmpid_id" value="<?=$cmpid_id?>">
                                        <label for="Designation" style="color: blue;">* Create a Request for Permission to Edit Contact Details of this Company</label>
                                        <textarea style="height: 310px;" required name="permission_request" class="form-control" id="permission_request"></textarea>
                                        </div>
                                    <center> <button type="submit" class="btn btn-primary">Request</button></center>
                                    </form>
                                    <?php }else{ ?>
                                       <div class="card requestcard p-2">
                                       <table class="table table-striped">
                                            <tbody>
                                                <tr class="table-primary">
                                                    <td><strong>Company Name</strong></td>
                                                    <td><strong><?=$cmData[0]->compname;?></strong></td>
                                                </tr>
                                                <tr class="table-primary">
                                                    <td><strong>Request Type</strong></td>
                                                    <td><?= $requestData[0]->request_type?></td>
                                                </tr>
                                                <tr class="table-secondary">
                                                    <td><strong>Request Meesage</strong></td>
                                                    <td><?= $requestData[0]->remarks?></td>
                                                </tr>
                                                <tr class="table-success">
                                                    <td><strong>Create Date</strong></td>
                                                    <td><?= $requestData[0]->created_at?></td>
                                                </tr>
                                                <tr class="table-warning">
                                                    <td><strong>Request Status</strong></td>
                                                    <td><?php 
                                                    if($requestData[0]->status == 0){
                                                        echo "<span class='p-1 bg-secondary'>Pending</span>";
                                                    }else if($requestData[0]->status == 1){
                                                        echo "<span class='p-1 bg-success'>Approved</span>";
                                                    }else if($requestData[0]->status == 2){
                                                        echo "<span class='p-1 bg-danger'>Reject</span>";
                                                    }
                                                    ?></td>
                                                </tr>
                                                <?php  if($requestData[0]->status == 0){ ?>
                                                <tr class="table-info text-center">
                                                    <td colspan="2" style="color:navy;"><strong>* Wait For Approval</strong></td>
                                                </tr>
                                                <?php  }else if($requestData[0]->status == 2){ ?>
                                                    <tr class="table-info text-center">
                                                    <td colspan="2" style="color:red;">
                                                    <strong>Reject Message</strong> <br>
                                                    <hr>
                                                    <?= $requestData[0]->apr_remarks ?></td>
                                                </tr>
                                              <?php  } ?>
                                            </tbody>
                                        </table>
                                       </div>
                                    <?php } ?>
                                </div>
                          
                            <div class="col-md-6">
                                 <div class="card p-2">
                                 <img class="img-fluide" src="https://img.freepik.com/premium-vector/man-tourist-request-special-accommodations-services-discover-new-horizon-travel-tourism-start-adventure-concept_48369-46279.jpg" alt="">
                                 </div>                   
                            </div>
                           </div>
                           
                        <?php } ?>
                      </div>
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
    <script>
      $(document).ready(function () {
          // Add new contact details section
          $(document).on("click", ".addContact", function () {
              let newContact = $("#contactDetails").first().clone(); // Clone the first card
              newContact.find("input, select").val(""); // Clear inputs and selects
              $("#contactDetailsContainer").append(newContact); // Append new card to the container
          });
      
          // Remove a contact details section
          $(document).on("click", ".removeContact", function () {
              if ($(".contactDetails").length > 1) {
              $(this).closest(".contactDetails").remove();
              } else {
              alert("You need at least one contact detail section!");
              }
          });
          });
      
    </script>
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
      // $("#example1").DataTable({
      //   "responsive": false, "lengthChange": false, "autoWidth": false,
      //   "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
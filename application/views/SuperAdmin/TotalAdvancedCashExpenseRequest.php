<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Our Team Cash Expense Report | STEM APP | WebAPP</title>
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
      .commentbox{
      background: #28a745;
      padding: 2px;
      }
      .modal-content {
      box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .5);
      box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
      }
      img.billesimage {
      padding: 5px;
      border: 1px solid cadetblue;
      box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
      padding: 3px;
      margin: 4px;
      }
      .cat1{background:#effaf8!important}.cat2{background:#fef2f4!important}.cat3{background:#fffaef!important}.cat4{background:#eefbf5!important}.cat5{background:#f7f3ff!important}.cat6{background:#fff7ef!important}.cat7{background:#f1fbff!important}.cat8{background:#fff0f8!important}
      td.billtd {
      display: flex;
      }
      td.billtd a{
      padding: 4px;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container-fluid">
            <?php
              if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
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
          </div>
        </div>
        <?php date_default_timezone_set("Asia/Calcutta");
         // dd($cashexpenseData);
        ?>
        <section class="content">
          <div class="container-fluid">
            <div>
              <form class="p-3" method="POST" action="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/$code/$suserid/$sdate/$edate" ?>">
                <input type="date" name="sdate" class="mr-2" value="<?= $sdate; ?>">
                <input type="date" name="edate" class="mr-2" value="<?= $edate; ?>">
                <button type="submit" class="bg-primary text-light">Filter</button>
              </form>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header bg-info">
                    <center>
                      <h3 class="text-center">
                        <?php 
                          $udetailname = $this->Menu_model->get_userbyid($suserid)[0]->name;
                          
                          if($code ==1){
                            $filter = 'All Request';
                          }else if($code ==2){
                            $filter = "Manager Side Approved";
                          }else if($code ==3){
                            $filter = "Manager Side Pending";
                          }else if($code ==4){
                          $filter = "Manager Side Rejected";
                          }else if($code ==5){
                          $filter = "Admin Side Approved";
                          }else if($code ==6){
                          $filter = "Admin Side Pending";
                          }else if($code ==7){
                          $filter = "Admin Side Rejected";
                          }else if($code ==8){
                            $filter = "Admin Side Suspected";
                          }else if($code ==9){
                          $filter = "Account Side Approved";
                          }else if($code ==10){
                          $filter = "Account Side Pending";
                          }else if($code ==11){
                          $filter = "Account Side Rejected";
                          }else if($code ==12){
                            $filter = "Account Side Suspected";
                            }
                          ?>
                        <b>( <?= $udetailname; ?> ) Travel Expense Report</b>
                        <br>
                      </h3>
                      <h4>  ( <?= $filter; ?> )</h4>
                    </center>
                  </div>
                  <?php $curtype_id = $user['type_id']; ?>
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                          <div class="table-responsive">
                            <div class="table-responsive text-nowrap">
                              <div class="pdf-viwer">
                              <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="bg-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Expense</th>
                                    <th scope="col">Expense Remarks</th>
                                    <th scope="col">Bills</th>
                                    <th scope="col">Manager Verify</th>
                                    <th scope="col">Manager Verify By</th>
                                    <th scope="col">Manager Verify Remarks</th>
                                    <th scope="col">Admin Approved</th>
                                    <th scope="col">Account Approved</th>
                                    <th scope="col">View Details</th>
                                    <?php if($code == 8 && $curtype_id == 2){?>
                                      <th scope="col">Actions</th>
                                    <?php } ?>
                                </tr>
                                </thead>
                                    <tbody>
                                    <?php
                                        $j =1;$k=1;
                                        foreach($cashexpenseData as $data){
                                    
                                        if($j==8){
                                            $k=1;
                                        }
                                        ?>
                                    <tr class="cat<?=$k;?>">
                                        <th><?= $j ?></th>
                                        <td><?= $data->name ?></td>
                                        <td><?= $data->created_at ?></td>
                                        <td><?= $data->compname ?></td>
                                        <td><?= $data->expense ?> ₹ </td>
                                        <td><?= $data->expense_remarks ?> </td>
                                        <td class="billtd">
                                        <?php 
                                            $bills = $data->bills;
                                            $decodedArray = json_decode($bills, true);
                                            foreach($decodedArray as $bill){ 
                                            $fileUrl = base_url().$bill;
                                            $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                                            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            if (@getimagesize($fileUrl)) { ?>
                                        <img  onclick="ViewBills(<?= $j ?>,'<?= $fileUrl; ?>')" src="<?= $fileUrl ?>" width="100px" height="100px" class="billesimage" alt="Image">
                                        <a target="_BLANK" href="<?= $fileUrl; ?>">View</a>
                                        <?php } else {
                                            echo 'Image not found or not accessible.';
                                            }
                                            } elseif ($fileExtension === 'pdf') { ?>
                                        <iframe src="<?= $fileUrl ?>" width="100px" height="100px" class="billesimage"></iframe>
                                        <a target="_BLANK" href="<?= $fileUrl; ?>">View</a>
                                        <?php } else {
                                            echo 'The file is neither an image nor a PDF.';
                                            }
                                            ?>
                                        <?php } ?>
                                        </td>
                                        <td>
                                        <?php
                                            if($data->verify == 0){ ?>
                                        <span class="p-1 bg-warning mr-2">Pending</span>
                                        <?php }else if($data->verify == '1'){ ?>
                                        <span class="p-1 bg-success mr-2">Approved</span>
                                        <?php }elseif($data->verify == '2'){ ?>
                                        <span class="p-1 bg-danger mr-2">Rejected</span>
                                        <?php }?>
                                        </td>
                                        <td>
                                        <?php
                                            if($data->verify_by == 0){ ?>
                                        <span class="p-1 bg-warning mr-2">Pending</span>
                                        <?php }else if($data->verify_by !== ''){ ?>
                                        <?= $this->Menu_model->get_userbyid($data->verify_by)[0]->name ?>
                                        <?php }?>
                                        </td>
                                        <td>
                                        <?php
                                            if($data->verify_remarks ==''){ ?>
                                        <span class="p-1 bg-warning mr-2">Pending</span>
                                        <?php }else if($data->verify_remarks !== ''){ ?>
                                        <?= $data->verify_remarks; ?>
                                        <?php }?>
                                        </td>
                                        <td>
                                        <?php 
                                        
                                            if($data->admin_apr == 0){
                                                $admin_apr =  "<span class='bg-warning p-1'>Pending</span>";
                                            }else if($data->admin_apr == 1){
                                            $admin_apr =  "<span class='bg-success p-1'>Approved</span>";
                                            }else if($data->admin_apr == 2){
                                            $admin_apr =  "<span class='bg-danger p-1'>Rejected</span>";
                                            }else if($data->admin_apr == 3){
                                                $admin_apr =  "<span class='bg-danger p-1'>Suspected</span>";
                                            }
                                            echo $admin_apr;
                                            ?>
                                        </td>
                                        <td>
                                        <?php 
                                            if($data->account_apr == 0){
                                                $account_apr =  "<span class='bg-warning p-1'>Pending</span>";
                                            }else if($data->account_apr == 1){
                                            $account_apr =  "<span class='bg-success p-1'>Approved</span>";
                                            }else if($data->account_apr == 2){
                                            $account_apr =  "<span class='bg-danger p-1'>Rejected</span>";
                                            }else if($data->account_apr == 3){
                                            $account_apr =  "<span class='bg-danger p-1'>Suspected</span>";
                                            }
                                            echo $account_apr;
                                            ?>
                                        </td>
                                        <td>
                                        <a href="<?= base_url().'Menu/TravelCashExpenseAdvancedDetails/'.$data->id; ?>">View</a>
                                      </td>
                                      <?php if($code == 8 && $curtype_id == 2){?>
                                        
                                      <td>
                                        <?php  if($data->admin_apr == 3){ ?>
                                      <div>
                                            <p><a href="<?=base_url();?>Menu/CashExpenseApproved/<?= $data->id?>" class="btn btn-success mr-2" onclick="return confirm('Are you sure you want to Approved it?');" >Approve</a></p>
                                            <p><button type="button" class="btn btn-warning"  onclick="Suspected(<?= $j ?>,<?= $data->id?>,'Suspected')">Suspected</button></p>
                                            <p><button type="button" class="btn btn-danger"  onclick="Reject(<?= $j ?>,<?= $data->id?>,'Reject')">Reject</button></p>
                                          </div>
                                          <?php }?>
                                      </td>
                                    <?php } ?>
                                    </tr>
                                    <?php $j++;$k++; } ?>
                                    </tbody>
                                </table>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                 <!-- Modal -->
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Bills</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <a href="" id="viewbillimageatag">
                    <img src="" id="viewbillimage" class="img-fluid" alt="">
                    <iframe src="" id="pdfViewer" class="billesimage"></iframe>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <?php if($code == 8 && $curtype_id == 2){?>
            <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-danger">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="<?=base_url();?>Menu/CashExpenseReject" method="post" >
                      <input type="hidden" id="rejectid" value="" name="reject">
                      <div class="mb-3 mt-3">
                        <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                      </div>
                      <div class="form-group text-center">
                        <button type="submit" class="btn btn-success mt-2">Submit</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="exampleModalCenterdata1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-warning">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add Suspected Remark</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form action="<?=base_url();?>Menu/CashExpenseSuspected" method="post" >
                      <input type="hidden" id="suspectedid" value="" name="suspectedid">
                      <div class="mb-3 mt-3">
                        <textarea id="suspectedidreamrk" name="suspectedidreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                      </div>
                      <div class="form-group text-center">
                        <button type="submit" class="btn btn-success mt-2">Submit</button>
                      </div>
                    </form>
                  </div>
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
    </section>
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
      </div>
    </footer>
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'>
      function ViewBills(rid,fileUrl){
        var fileExtension = fileUrl.split('.').pop().toLowerCase(); 
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            $('#viewbillimage').attr('src', fileUrl).show();
            $('#viewbillimageatag').attr('href', fileUrl).show();
        } else if (fileExtension === 'pdf') {
            $('#pdfViewer').attr('src', fileUrl).show();
            $('#viewbillimageatag').attr('href', fileUrl).show();
        } else {
            alert('The file is neither an image nor a PDF.');
        }
        $('#exampleModalCenter').modal('show');
      }
      
      function Reject(mid,id,val){
      $('#exampleModalCenterdata').modal('show');
      $('#rejectid').val(id);
      }
       function Suspected(mid,id,val){
      $('#exampleModalCenterdata1').modal('show');
      $('#suspectedid').val(id);
      }
      
      
    </script>
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
    <script src="https://cdn.tiny.cloud/1/qagffr3pkuv17a8on1afax661irst1hbr4e6tbv888sz91jc/tinymce/6/tinymce.min.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
    </script>
  </body>
</html>
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
          <?php 
          date_default_timezone_set("Asia/Calcutta"); 
           
          ?>
        <section class="content">
          <div class="container-fluid">
          <div>
                <form class="p-3" method="POST" action="<?= base_url().'Menu/OurTeamCashExpenseReport' ?>">
                    <input type="date" name="sdate" class="mr-2" value="<?= $sdate; ?>">
                    <input type="date" name="edate" class="mr-2" value="<?= $edate; ?>">
                <button type="submit" class="bg-primary text-light">Filter</button>
            </form>
              </div>

              <?php 
           // dd($travelCashData);
          ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header bg-info">
                    <h3 class="text-center">
                      <center><b>Team Cash Expense Report</b></center>
                    </h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <fieldset>
                          <div class="table-responsive">
                            <div class="table-responsive">
                              <div class="pdf-viwer">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th>S.No.</th>
                                      <th>Name</th>
                                      <th>Total Request</th>
                                      <th>Total Pending</th>
                                      <th>Total Reject</th>
                                      <th>Total Suspected</th>

                                      <th>Manager Side Approved</th>
                                      <th>Manager Side Pending</th>
                                      <th>Manager Side Reject</th>

                                      <th>Admin Side Approved</th>
                                      <th>Admin Side Pending</th>
                                      <th>Admin Side Reject</th>
                                      <th>Account Side Suspected</th>

                                      <th>Account Side Approved</th>
                                      <th>Account Side Pending</th>
                                      <th>Account Side Reject</th>
                                      <th>Account Side Suspected</th>
                                      <th>Total Current Cash in User Wallet (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Expense Request (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Manager Side Approved Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Manager Side Pending Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Manager Side Reject Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>

                                      <th>Totoal Admin Side Approved Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Admin Side Pending Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Admin Side Reject Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Admin Side Suspected Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>

                                      <th>Totoal Account Side Approved Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Account Side Pending Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Account Side Reject Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>
                                      <th>Totoal Account Side Suspected Expense (<i class="fa fa-inr" aria-hidden="true"></i>)</th>

                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1; 
                                      foreach($travelCashData as $cash){ 
                                      ?>
                                    <tr>
                                    <td><?= $i; ?></td>
                                    <td><?= $cash->name; ?></td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/1/$cash->user_id/$sdate/$edate" ?>"><?= $cash->total; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/13/$cash->user_id/$sdate/$edate" ?>"><?= $cash->total_pending; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/14/$cash->user_id/$sdate/$edate" ?>"><?= $cash->total_reject; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/15/$cash->user_id/$sdate/$edate" ?>"><?= $cash->total_suspect; ?></a> </td>

                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/2/$cash->user_id/$sdate/$edate" ?>"><?= $cash->manager_approved; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/3/$cash->user_id/$sdate/$edate" ?>"><?= $cash->manager_pending; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/4/$cash->user_id/$sdate/$edate" ?>"><?= $cash->manager_reject; ?></a> </td>

                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/5/$cash->user_id/$sdate/$edate" ?>"><?= $cash->admin_approved; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/6/$cash->user_id/$sdate/$edate" ?>"><?= $cash->admin_pending; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/7/$cash->user_id/$sdate/$edate" ?>"><?= $cash->admin_reject; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/8/$cash->user_id/$sdate/$edate" ?>"><?= $cash->admin_suspected; ?></a> </td>

                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/9/$cash->user_id/$sdate/$edate" ?>"><?= $cash->account_approved; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/10/$cash->user_id/$sdate/$edate" ?>"><?= $cash->account_pending; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/11/$cash->user_id/$sdate/$edate" ?>"><?= $cash->account_reject; ?></a> </td>
                                    <td> <a href="<?= base_url()."Menu/TotalAdvancedCashExpenseRequest/12/$cash->user_id/$sdate/$edate" ?>"><?= $cash->account_suspected; ?></a> </td>
                                    <td> <?= $cash->ucash; ?></td>
                                    <td> <?= $cash->total_cash_expense; ?></td>
                                    <td> <?= $cash->total_approved_cash_form_manager; ?></td>
                                    <td> <?= $cash->total_pending_cash_form_manager; ?></td>
                                    <td> <?= $cash->total_reject_cash_form_manager; ?></td>

                                    <td> <?= $cash->total_approved_cash_form_admin; ?></td>
                                    <td> <?= $cash->total_pending_cash_form_admin; ?></td>
                                    <td> <?= $cash->total_reject_cash_form_admin; ?></td>
                                    <td> <?= $cash->total_suspect_cash_form_admin; ?></td>

                                    <td> <?= $cash->total_approved_cash_form_account; ?></td>
                                    <td> <?= $cash->total_pending_cash_form_account; ?></td>
                                    <td> <?= $cash->total_reject_cash_form_account; ?></td>
                                    <td> <?= $cash->total_suspect_cash_form_account; ?></td>

                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            </form>            <!--END OF FORM ^^-->
                        </fieldset>
                        </div>
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
    <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background: red; color: white;">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <form action="<?=base_url();?>Menu/TravelAdavancedReject" method="post" >
                              <input type="hidden" id="rejectid" value="" name="reject">
                              <div class="mb-3 mt-3">
                                <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                              </div>
                              <div class="form-group text-center">
                                <button type="submit" class="btn btn-success mt-2">Reject</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
    <div class="modal fade" id="exampleModalCenterdataApprove" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background: green; color: white;">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Approve Remark</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <form action="<?=base_url();?>Menu/TravelAdavancedApprove" method="post" >
                              <input type="hidden" id="approveid" value="" name="approveid">
                              <div class="mb-3 mt-3">
                                <textarea id="approve_remarks" name="approve_remarks" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                              </div>
                              <div class="form-group text-center">
                                <button type="submit" class="btn btn-success mt-2">Approve</button>
                              </div>
                            </form>
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
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type='text/javascript'>

        function Reject(mid,id,val){
          $('#exampleModalCenterdata').modal('show');
          $('#rejectid').val(id);
        }

        function Approve(mid,id,val){
          $('#exampleModalCenterdataApprove').modal('show');
          $('#approveid').val(id);
        }
        </script>
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
    <script src="https://cdn.tiny.cloud/1/qagffr3pkuv17a8on1afax661irst1hbr4e6tbv888sz91jc/tinymce/6/tinymce.min.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

</script>



  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Data After Created Between Dates | STEM APP | WebAPP</title>
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
      .card.cardbxs{box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;}
      form.p-3 {
    box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;
    
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <?php 
          $bd = $this->Menu_model->get_userbyaid($uid);
          ?>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 text-center mt-3">
                <div class="card cardbxs p-3">
                <?php  
               
                $readableKey = ucwords(str_replace('_', ' ', $sendtypes)); ?>
                <h3 class="m-0">Company Created Between Dates</h3>
                <center><hr style="width:300px"></center>
                 <i><span><strong><?=$sd;?></strong> &nbsp;&nbsp; <b>To</b> &nbsp;&nbsp; <strong><?=$ed;?></strong></span></i>
                 <center><hr style="width:300px"></center>
                 <strong><span><?=$readableKey;?></span></strong>
                 <center><hr style="width:300px"></center>
                 <strong>
                    <?php if($targetuid == 0){ ?>
                        <span>All</span>
                   <?php }else{ ?>
                    <span><?= $this->Menu_model->get_userbyid($targetuid)[0]->name; ?></span>
                   <?php } ?>
                 </strong>
                </div>
              </div>
              <div class="col-12">

                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"></h3>
                  </div>
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <fieldset>
                          <div class="table-responsive">
                            <div class="table-responsive text-nowrap">
                              <div class="pdf-viwer">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class='thead-dark'>
                                    <tr>
                                      <th>#</th>
                                      <th>Name</th>
                                      <th>Types Name</th>
                                      <th>Check BY</th>
                                      <th>Company Name</th>
                                      <th>By Task</th>
                                      <th>Created Date</th>
                                      <th>Is Admin Approved</th>
                                      <th>Approved By</th>
                                      <th>Approved Date</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1; foreach($companyInfoCBDs as $companyInfoCBD){ ?>
                                    <tr>
                                      <td><?=$i?></td>
                                      <td><?=$companyInfoCBD->user_name?></td>
                                      <td><?=$companyInfoCBD->type_name?></td>
                                      <td><?=$companyInfoCBD->checkbyname?></td>
                                      <td><?=$companyInfoCBD->compname?></td>
                                      <td><?=$companyInfoCBD->by_task_name?></td>
                                      <td><?=$companyInfoCBD->created_at?></td>
                                      <td><?php
                                      $is_admin_approved = $companyInfoCBD->is_admin_approved;
                                      if($is_admin_approved == 1){
                                        echo "<span class='bg-success p-1'>Approved</span>";
                                      }else if($is_admin_approved == 2){
                                        echo "<span class='bg-danger p-1'>Need&nbsp;To&nbsp;Update</span>";
                                      }else if($is_admin_approved == ''){
                                        echo "<span class='bg-warning p-1'>Pending</span>";
                                      }
                                      
                                      ?></td>
                                      <td><?=$companyInfoCBD->approved_by_name?></td>
                                      <td><?=$companyInfoCBD->apr_date?></td>
                                   
                                    </tr>
                                    <?php $i++; } ?>  
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
    <!-- /.container-fluid -->
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
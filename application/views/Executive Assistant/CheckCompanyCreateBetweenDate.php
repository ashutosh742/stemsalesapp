<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Company Created Between Dates | STEM APP | WebAPP</title>
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
                <h3 class="m-0">Company Created Between Dates</h3>
                <center><hr style="width:300px"></center>
                 <i><span><strong><?=$sd;?></strong> &nbsp;&nbsp; <b>To</b> &nbsp;&nbsp; <strong><?=$ed;?></strong></span></i>
                </div>
              </div>
              <div class="col-12">
                <div class="formcard text-right">
                  <form class="p-3" method="POST" action="<?=base_url()?>menu/CheckCompanyCreateBetweenDate"><input type="date" name="sdate" class="mr-2" value="<?=$sd?>"><input type="date" name="edate" class="mr-2" value="<?=$ed?>">
                    <button type="submit" class="bg-primary text-light">Filter</button>
                  </form>
                </div>
                <hr>
                <?php 
                // Initialize counters
                $total_create_company = 0;
                $total_approved = 0;
                $pending_for_approved = 0;
                $need_to_update = 0;
                $form_research = 0;
                $form_barg_in_meeting = 0;

                // Loop through the array and calculate totals
                foreach ($companyInfoCBDs as $item) {
                    $total_create_company += $item->total_create_company;
                    $total_approved += $item->total_approved;
                    $pending_for_approved += $item->pending_for_approved;
                    $need_to_update += $item->need_to_update;
                    $form_research += $item->form_research;
                    $form_barg_in_meeting += $item->form_barg_in_meeting;
                }

                // Display the results
               
              
                
                
                
                //dd($companyInfoCBDs);
                
                ?>

                <div class="card2">
                    <div class="row">
                        <div class="col-md-4">
                           <div class="card text-center">
                            <h5>Total Create Company</h5>
                           <strong>
                            <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/total_create_company/"><?=$total_create_company;?></a>
                           </strong>
                           </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                            <h5>Total Approved</h5>
                            <strong>
                            <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/total_approved/"><?= $total_approved; ?></a>
                            </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                            <h5>Total Pending for Approval</h5>
                           <strong> 
                           <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/pending_for_approved/"><?=$pending_for_approved;?></a>
                           </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                            <h5>Total Need to Update</h5>
                            <strong>
                            <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/need_to_update/"><?= $need_to_update;?></a>
                            </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                            <h5>From Research</h5>
                            <strong>
                            <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/form_research/"><?= $form_research;?></a>
                            </strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                            <h5>From Barg Meetings</h5>
                            <strong>
                            <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/form_barg_in_meeting/"><?= $form_barg_in_meeting;?></a>
                            </strong>
                            </div>
                        </div>
                      
                    </div>
                </div>


                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"></h3>
                  </div>
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
                                      <th>#</th>
                                      <th>Name</th>
                                      <th>Types Name</th>
                                      <th>Total Create Company</th>
                                      <th>Total Approved</th>
                                      <th>Pending for Approved</th>
                                      <th>Need to Update</th>
                                      <th>Form Barg in Meeting</th>
                                      <th>Form Research</th>
                                      <th>Check BY</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1; foreach($companyInfoCBDs as $companyInfoCBD){
                                        $tar_user_id = $companyInfoCBD->tar_user_id;
                                        ?>
                                    <tr>
                                      <td><?=$i?></td>
                                      <td><?=$companyInfoCBD->user_name?></td>
                                      <td><?=$companyInfoCBD->type_name?></td>
                                      <td>
                                      <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/total_create_company/<?=$tar_user_id?>">
                                        <?=$companyInfoCBD->total_create_company?>
                                       </a>
                                    </td>
                                      <td>
                                      <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/total_approved/<?=$tar_user_id?>">
                                        <?=$companyInfoCBD->total_approved?>
                                        </a>
                                    </td>
                                      <td>
                                      <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/pending_for_approved/<?=$tar_user_id?>">
                                      <?=$companyInfoCBD->pending_for_approved?>
                                      </a>
                                    </td>
                                      <td>
                                      <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/need_to_update/<?=$tar_user_id?>">
                                      <?=$companyInfoCBD->need_to_update?>
                                      </a>
                                    </td>
                                      <td>
                                      <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/form_barg_in_meeting/<?=$tar_user_id?>">
                                      <?=$companyInfoCBD->form_barg_in_meeting?>
                                      </a>
                                    </td>
                                      <td>
                                      <a href="<?=base_url()?>Menu/CompanyDetailsAfterAdd/<?=$sd;?>/<?=$ed;?>/form_research/<?=$tar_user_id?>">  
                                      <?=$companyInfoCBD->form_research?>
                                      </a>
                                    </td>
                                      <td><?=$companyInfoCBD->admin_name?></td>
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
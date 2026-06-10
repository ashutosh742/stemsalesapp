<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deleted 🏢 Company 🗑️ Between 📅 Start and End Dates ⏳ | STEM APP | WebAPP</title>
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
      .card.cardbxs {
        box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
      }
      form.p-3 {
        box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;
      }
      .bg-total-create-company {
        background-color: #ffcccb; /* Light red */
      }
      .bg-total-approved {
        background-color: #d4edda; /* Light green */
      }
      .bg-pending-for-approval {
        background-color: #fff3cd; /* Light yellow */
      }
      .bg-need-to-update {
        background-color: #d1e7dd; /* Light cyan */
      }
      .bg-from-research {
        background-color: #e2e3e5; /* Light gray */
      }
      .bg-from-barg-meetings {
        background-color: #cce5ff; /* Light blue */
      }
      .bg-table-row {
        background-color: #f8d7da; /* Light red for table rows */
      }

      .card{
        background: linear-gradient(to right, #e8f5e9, #ffffff)!important; 
        border-radius: 12px; 
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;
      }

      tr,td{
        font-weight: 500;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
     <?php $this->load->view($dep_name.'/nav.php');?>
      <div class="content-wrapper">
        <?php
          $bd = $this->Menu_model->get_userbyaid($uid);
        ?>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 text-center mt-3">
                <div class="card cardbxs p-3" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h3 class="m-0" style="color: #1d3f06;">Deleted 🏢 Company 🗑️ Between 📅 Dates ⏳</h3>
                  <center><hr style="width:300px">
                  <mark>
                    <i style="color: #1d3f06;"><span><strong><?=$sd;?></strong> &nbsp; <b>To</b> &nbsp;<strong><?=$ed;?></strong></span></i>
                  </mark>
                  </center>
                </div>
              </div>
              <div class="col-12">
                <div class="formcard text-right">
                  <form class="p-3" method="POST" action="<?=base_url()?>menu/CheckRemoveCompanyLogsBetweenDate" style="align-items: center; justify-content: center; display: flex ;">
                    <input type="date" name="sdate" class="mr-2 form-control" style="width: 150px;" value="<?=$sd?>">
                    <input type="date" name="edate" class="mr-2 form-control" style="width: 150px;" value="<?=$ed?>">
                    <button type="submit" class="btn btn-primary text-light">Filter</button>
                  </form>
                </div>
                <hr>

                <?php 
                // dd($companyInfoCBDs);
                ?>
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
                                        <th>#️⃣</th>
                                        <th>👤 Name</th>
                                        <th>🗑️ Total Deleted Company</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1; foreach($companyInfoCBDs as $companyInfoCBD){  
                                    
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
                                      <td><?=$i?></td>
                                      <td>
                                        <a target="_BLANK" href="<?=base_url()?>Reports/UserDetails/<?=$companyInfoCBD->user_id;?>">
                                          <?=$companyInfoCBD->username?>
                                        </a>
                                      </td>
                                      <td>
                                        <a target="_BLANK" class="badge badge-pill badge-success p-2" href="<?=base_url()?>Reports/CheckRemoveCompanyLogsBetweenDateLogs/<?=$companyInfoCBD->user_id;?>/<?=$sd;?>/<?=$ed;?>">
                                          <?=$companyInfoCBD->total_deleted?>
                                        </a>
                                      </td>
                                    </tr>
                                    <?php $i++; } ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </fieldset>
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

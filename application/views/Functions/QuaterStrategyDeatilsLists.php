<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STEM APP | WebAPP</title>
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
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">Quarter Strategy Details</h3>
                    <p class="text-center m-3"><strong><?=$fyear?> - <?= $cquarter; ?></strong></p>
                    <p class="text-center m-3"><strong><?=$cq_start?> - <?= $cq_end; ?></strong></p>
                    <p class="text-center m-3"><strong>
                        <?= ucwords(str_replace("_", " ", $rtype));?>
                </strong></p>
                <p class="text-center m-3"><strong> <?= $targetUserDatas[0]->name;?></strong></p>
                  </div>
                  <hr>
                  <!-- /.card-header -->
                  <div class="card-body1" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <div class="container-fluid body-content">

                        <div class="table-responsive text-nowrap">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="table-dark text-center">
                                   <tr>
                                    <th> Sr. No.</th>
                                    <th>👤 BD Name</th>
                                    <th>📅 Financial Year</th>
                                    <th>📅 In Quarter</th>
                                    <th>🏢 Company Name</th>
                                    <th>🆔 CID</th>
                                    <th>🆔 Current Status</th>

                                    <!-- <th>Prospecting Funnel</th>
                                    <th>Proposal To Be Sent Review</th>
                                    <th>Proposal to Be Sent - Target</th>
                                    <th>Closure Pipeline</th> -->


                                    <th>✅ RP Meeting Done</th>
                                    <th>⏳ Pending for Meeting</th>
                                    <th>📤 Proposal Sent</th>
                                    <th>📥 Pending Proposal</th>
                                    <th>💰 Closure Done</th>
                                    <th>⚠️ Pending Closure</th>
                                </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php $k=1; foreach($gettimeline as $row):

                                      $r = rand(150, 255);
                                        $g = rand(150, 255);
                                        $b = rand(150, 255);
                                        $backgroundColor = "rgba($r, $g, $b,.2)";

                                        $hue = rand(0, 360);
                                        $saturation = rand(60, 100);
                                        $lightness = rand(30, 45);

                                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                        ?>
                                    <tr style="background-color: <?php echo $backgroundColor; ?>;">
                                            <td><?= htmlspecialchars($k) ?></td>
                                            <td><?= htmlspecialchars($row->main_bd_name) ?></td>
                                            <td><?= htmlspecialchars($row->quater_strategy_fyear) ?></td>
                                            <td><?= htmlspecialchars($row->quater_strategy_in_quater) ?></td>
                                            

                                            <td> <a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?= $row->cid ?>"><?= htmlspecialchars($row->compname) ?></a></td>
                                             <td> <a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?= $row->cid ?>"><?= htmlspecialchars($row->cid) ?></a></td>

                                             <td><?= htmlspecialchars($row->current_status_name) ?></td>
                                             <?php /*
                                             <td><?= htmlspecialchars($row->prospecting_funnel) ?></td>
                                             <td><?= htmlspecialchars($row->proposal_to_be_sent_review) ?></td>
                                             <td><?= htmlspecialchars($row->proposal_to_be_sent_target) ?></td>
                                             <td><?= htmlspecialchars($row->closure_pipeline_october) ?></td>

                                             */ ?>

                                            <td>
                                                <?php 
                                                if($row->total_rp_meeting_done == 'Yes'){
                                                    $badgeMessage = 'text-success';
                                                }else{
                                                    $badgeMessage = 'text-danger';
                                                } 
                                                
                                                echo $tblc1_id;
                                                
                                                ?>
                                                <span class="p-2 <?= $badgeMessage ?> mt-1"><?= $row->total_rp_meeting_done ?></span>
                                            </td>
                                            <td> 
                                                 <?php 
                                                if($row->pending_for_meeting == 'No'){
                                                    $badgeMessage = 'text-success';
                                                }else{
                                                    $badgeMessage = 'text-danger';
                                                } ?>
                                                <span class="p-2 <?= $badgeMessage ?> mt-1"><?= $row->pending_for_meeting ?></span>
                                            </td>
                                            <td>
                                                 <?php 
                                                if($row->proposal_sent == 'Yes'){
                                                   $badgeMessage = 'text-success';
                                                }else{
                                                    $badgeMessage = 'text-danger';
                                                } ?>
                                                <span class="p-2 <?= $badgeMessage; ?>  mt-1"><?= $row->proposal_sent ?></span>
                                            </td>
                                            <td>
                                                 <?php 
                                                if($row->pending_proposal_sent == 'No'){
                                                    $badgeMessage = 'text-success';
                                                }else{
                                                    $badgeMessage = 'text-danger';
                                                } ?>
                                                <span class="p-2 <?= $badgeMessage ?> mt-1"><?= $row->pending_proposal_sent ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                if($row->total_closure == 'Yes'){
                                                    $badgeMessage = 'text-success';
                                                }else{
                                                    $badgeMessage = 'text-danger';
                                                } ?>
                                                <span class="p-2 <?= $badgeMessage ?> mt-1"><?= $row->total_closure ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                if($row->total_pending_closure == 'Yes'){
                                                    $badgeMessage = 'text-danger';
                                                }else{
                                                    $badgeMessage = 'text-success';
                                                } ?>
                                                <span class="p-2 <?= $badgeMessage ?> mt-1"><?= $row->total_pending_closure ?></span>
                                            </td>
                                        </tr>
                                    <?php $k++; endforeach; ?>
                                </tbody>
                            </table>
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
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'>

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
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>

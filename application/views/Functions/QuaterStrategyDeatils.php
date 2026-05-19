<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
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
  
      /* th.text-capitalize:nth-child(1),
      td:nth-child(1),
      th.text-capitalize:nth-child(3),
      td:nth-child(3) {
      position: sticky;
      left: 0;
      color: white;
      z-index: 10;
      }
      tbody tr td:nth-child(1),
      tbody tr td:nth-child(3) {
      background-color: white;
      color: black;
      } */
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
          <div class="container-fluid mt-2">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">Quater Strategy Details</h3>
                    <p class="text-center m-3"><strong><?=$fyear?> - <?= $cquarter; ?></strong></p>
                    <p class="text-center m-3"><strong><?=$cq_start?> - <?= $cq_end; ?></strong></p>
                  </div>
                  <hr>
                  <!-- /.card-header -->
                  <div class="card-body1" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <div class="container-fluid body-content">

                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                         <form action="<?=base_url()?>Menu/QuaterStrategyDeatils" style="display:flex; align-items:center; gap:10px; background:#f8f9fa; padding:10px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                                <div class="col" style="flex:1;">
                                    <label style="font-weight:600; font-size:14px;">📊 Select Financial Year:</label>
                                    <select class="form-control" name="fyear" required aria-label="Small select example" style="width:100%; padding:6px; border-radius:6px; border:1px solid #ccc;">
                                        <option selected disabled>--Select--</option>
                                       <?php 
                                        $fyearLists = $this->Menu_model->GetQuarterYearINDatabase();
                                        foreach($fyearLists as $fyearList){
                                         ?>
                                            <option value="<?= $fyearList->fyear ?>">🌟 <?= $fyearList->fyear ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col" style="flex:1;">
                                    <label style="font-weight:600; font-size:14px;">📁 Select Quarter:</label>
                                    <select name="in_quater" required class="form-control" aria-label="Small select example" style="width:100%; padding:6px; border-radius:6px; border:1px solid #ccc;">
                                        <option selected disabled>--Select--</option>
                                        <?php 
                                        $in_quaterss = $this->Menu_model->GetQuarterINDatabase();
                                        foreach($in_quaterss as $in_quaters){
                                         ?>
                                            <option value="<?= $in_quaters->in_quater ?>">🌟 <?= $in_quaters->in_quater ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col" style="display:flex; align-items:end;">
                                    <button class="mt-4" type="submit" style="background-color:#007bff; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer; transition:0.3s;">
                                        🔍 Filter
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                        <div class="table-responsive text-nowrap">
                            <table id="example1" class="table table-striped table-hover">
                                <thead class="table-dark text-center">
                                   <tr>
                                    <th> Sr. No.</th>

                                    <th>👤 BD Name</th>
                                    <th>👤 Financial Year</th>
                                    <th>👤 In Quarter</th>

                                    <!-- <th>📝 Total BD Company</th> -->
                                    <th>📝 Total Company</th>
                                    <th>📝 Total Prospecting funnel</th>
                                    <th>📝 Total Proposal to be Sent Review</th>
                                    <th>📝 Total Proposal to be Sent Target</th>
                                    <th>📝 Total Closure Pipeline October</th>
                                    <th>✅ RP Meeting Done</th>
                                    <th>⏳ Pending for Meeting</th>
                                    <th>📤 Proposal Sent</th>
                                    <th>📥 Pending Proposal</th>
                                    <th>💰 Total Closure</th>
                                    <th>⚠️ Pending Closure</th>
                                </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php $k=1; foreach($gettimeline as $row):
                                        
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($k) ?></td>

                                            <td><?= htmlspecialchars($row->main_bd_name) ?></td>
                                            <td><?= htmlspecialchars($row->quater_strategy_fyear) ?></td>
                                            <td><?= htmlspecialchars($row->quater_strategy_in_quater) ?></td>

                                          <?php /*
                                            <td><a target="_BLANK" href="<?=base_url()?>Reports/FunnelDetails/total/<?= $row->main_bd_id ?>/userwise" class="p-2 badge badge-pill badge-info mt-1"><?= $row->total_bd_funnel ?></a></td>

                                            */ ?>

                                            
                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-info mt-1"><?= $row->total ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_prospecting_funnel/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-info mt-1"><?= $row->total_prospecting_funnel ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_proposal_to_be_sent_review/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-info mt-1"><?= $row->total_proposal_to_be_sent_review ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_proposal_to_be_sent_target/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-info mt-1"><?= $row->total_proposal_to_be_sent_target ?></a></td>
                                            
                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_closure_pipeline_october/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-info mt-1"><?= $row->total_closure_pipeline_october ?></a></td>
                                            
                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_rp_meeting_done/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-success mt-1"><?= $row->total_rp_meeting_done ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/pending_for_meeting/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-danger mt-1"><?= $row->pending_for_meeting ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/proposal_sent/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-success mt-1"><?= $row->proposal_sent ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/pending_proposal_sent/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-danger mt-1"><?= $row->pending_proposal_sent ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_closure/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-success mt-1"><?= $row->total_closure ?></a></td>

                                            <td><a target="_BLANK" href="<?=base_url()?>Menu/QuaterStrategyDeatilsLists/total_pending_closure/<?= $row->main_bd_id ?>/<?= $fyear ?>/<?= $cquarter ?>/<?= $cq_start ?>/<?= $cq_end ?>" class="p-2 badge badge-pill badge-danger mt-1"><?= $row->total_pending_closure ?></a></td>

                                        </tr>
                                    <?php $k++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>



                        <hr>
                        <center>
                          <h3>Our & Team Quarter Strategy Details</h3>
                        </center>
                      <hr>                

                        <?php 
                        $gettimelines        =  $this->Menu_model->GetQuaterStrategyCountsDatasLists("total_list",$user['user_id'],$cq_start,$cq_end,$cquarter,$fyear);
                        ?>

                        <div class="table-responsive text-nowrap">
                            <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="table-dark text-center">
                                   <tr>
                                    <th> Sr. No.</th>
                                    <th>👤 BD Name</th>
                                    <th>📅 Financial Year</th>
                                    <th>📅 In Quarter</th>
                                    <th>🏢 Company Name</th>
                                    <th>🆔 CID</th>
                                    <th>🆔 Current Status</th>

                                    <th>Prospecting Funnel</th>
                                    <th>Proposal To Be Sent Review</th>
                                    <th>Proposal to Be Sent - Target</th>
                                    <th>Closure Pipeline</th>


                                    <th>✅ RP Meeting Done</th>
                                    <th>⏳ Pending for Meeting</th>
                                    <th>📤 Proposal Sent</th>
                                    <th>📥 Pending Proposal</th>
                                    <th>💰 Closure Done</th>
                                    <th>⚠️ Pending Closure</th>
                                </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php $k=1; foreach($gettimelines as $row):

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
                                           
                                             <td><?= htmlspecialchars($row->prospecting_funnel) ?></td>
                                             <td><?= htmlspecialchars($row->proposal_to_be_sent_review) ?></td>
                                             <td><?= htmlspecialchars($row->proposal_to_be_sent_target) ?></td>
                                             <td><?= htmlspecialchars($row->closure_pipeline_october) ?></td>

                                             

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

      $("#example2").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calling Outcome & RP Proposal Conversion Report | STEM APP | WebAPP</title>
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
       <br>
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card-details">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">Calling Outcome & RP Proposal Conversion Report</h3>
                  </div>
                  <hr>
                  <?php if($user['type_id'] != 3): ?>
                  <div class="row">
                    <hr>
                    <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 60%;">
                      <?php 
                        $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                        $taskActions      = $this->Menu_model->get_action();
                        $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                        ?>
                      <div class="col text-center formcenterData">
                        <div>
                          <hr>
                          <form action="<?=base_url()?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_Report" method="post" class="mt-3">
                            <div class="row mb-4">
                              <div class="col">
                                <label for="selectedDate">Date</label>
                                <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 250px;" class="form-control">
                              </div>
                             <div class="col">
                                <label for="selectedDate">End Date</label>
                                <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                                </div>
                              <div class="col">
                                <label for="selectedDate">Select Admin</label>
                                <select id="admin_id_filter" name="admin_id_filter" class="form-control">
                                  <?php if($user['type_id'] == 2){ ?> 
                                  <option value="all">All</option>
                                  <?php } ?>
                                  <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                                  <option value="<?= $clusterPstData->user_id; ?>" <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                                    <?= $clusterPstData->name; ?>
                                  </option>
                                  <?php } ?>
                                </select>
                              </div>
                              <div class="col">
                                <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                                <label for="selectedDate">Select RM</label>
                                <select id="rm_filter" name="rm_filter" class="form-control">
                                  <?php if($user['type_id'] !== 3){ ?> 
                                  <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                  <?php } ?>
                                  <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                                  <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                                    <?= $getTeamsData->name; ?>
                                  </option>
                                  <?php }?>
                                </select>
                              </div>
                              <div class="col text-center">
                                <div class="form-group">
                                  <button type="submit" class="btn btn-primary p-1" style="margin-top: 33px; width: 230px;">Filter</button>
                                </div>
                              </div>
                            </div>
                          </form>
                          <hr>
                        </div>
                      </div>
                    </div>
                    <hr>
                  </div>
                  <!-- /.card-header -->
                  <?php endif; ?>
                </div>
              </div>
            </div>


            <hr>

            <?php // dd($mdata); ?>




  <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                        <th class="text-capitalize">#️⃣ Sr. NO</th>
                          
                          <th>🏢 CID</th>
                          <th>🏢 Company</th>
                          <th>🏢 Current Status</th>
                          <th>👤 Main BD </th>
                          <th>📅 Total Meetings</th>
                          <th>📄 Total Proposal Shared</th>

                          <th>📞 Total Planned Calls (After 1st RP)</th>
                          <th>✅ Total Connected Calls (After 1st RP)</th>
                          
                          <th>📞 Total Planned Calls (After Proposal)</th>
                          <th>✅ Total Connected Calls (After Proposal)</th>

                          <th>📞 Planned Calls BY MainBD (After 1st RP)</th>
                          <th>✅ Connected Calls BY MainBD (After 1st RP)</th>

                          <th>📞 Planned Calls BY ACM (After 1st RP)</th>
                          <th>✅ Connected Calls BY ACM (After 1st RP)</th>

                          <th>📞 Planned Calls BY CM (After 1st RP)</th>
                          <th>✅ Connected Calls BY CM (After 1st RP)</th>

                          <th>📞 Planned Calls BY PST (After 1st RP)</th>
                          <th>✅ Connected Calls BY PST (After 1st RP)</th>

                          <th>📞 Planned Calls BY RM (After 1st RP)</th>
                          <th>✅ Connected Calls BY RM (After 1st RP)</th>

                          <th>📞 Planned Calls BY Admin | Super Admin (After 1st RP)</th>
                          <th>✅ Connected Calls BY Admin | Super Admin (After 1st RP)</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($mdata as $row) {
                          $task_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= htmlspecialchars($i) ?></td>

                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>

                        <td><?= htmlspecialchars($row->current_status) ?></td>
                        <td><?= htmlspecialchars($row->mainbd_name) ?></td>
                        <td>
                           <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_meeting">
                            <?= $row->total_meeting ?>
                          </a>
                        </td>
                        <td>
                            <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/proposal_shared">
                            <?= $row->proposal_shared ?> 
                            </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp">
                            <?= $row->total_planned_call_after_first_rp ?> 
                            </a>
                        </td>
                        <td>
                            <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp">
                                <?= $row->total_call_connect_after_first_rp ?> 
                            </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_proposal">
                            <?= $row->total_planned_call_after_first_proposal ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_proposal">
                            <?= $row->total_call_connect_after_first_proposal ?>
                            </a>
                        </td>


                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp_by_mainbd">
                            <?= $row->total_planned_call_after_first_rp_by_mainbd ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp_by_mainbd">
                            <?= $row->total_call_connect_after_first_rp_by_mainbd ?>
                            </a>
                        </td>


                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp_by_acm">
                            <?= $row->total_planned_call_after_first_rp_by_acm ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp_by_acm">
                            <?= $row->total_call_connect_after_first_rp_by_acm ?>
                            </a>
                        </td>

                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp_by_cm">
                            <?= $row->total_planned_call_after_first_rp_by_cm ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp_by_cm">
                            <?= $row->total_call_connect_after_first_rp_by_cm ?>
                            </a>
                        </td>


                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp_by_pst">
                            <?= $row->total_planned_call_after_first_rp_by_pst ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp_by_pst">
                            <?= $row->total_call_connect_after_first_rp_by_pst ?>
                            </a>
                        </td>

                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp_by_rm">
                            <?= $row->total_planned_call_after_first_rp_by_rm ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp_by_rm">
                            <?= $row->total_call_connect_after_first_rp_by_rm ?>
                            </a>
                        </td>

                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_planned_call_after_first_rp_by_admin">
                            <?= $row->total_planned_call_after_first_rp_by_admin ?> 
                          </a>
                        </td>
                        <td>
                          <a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList/<?=$row->cid?>/<?=$sdate?>/<?=$edate?>/total_call_connect_after_first_rp_by_admin">
                            <?= $row->total_call_connect_after_first_rp_by_admin ?>
                            </a>
                        </td>


                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
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
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?= date("Y"); ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
    </script>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title> Advance vs Meeting Analysis with Upcoming Advance 🚀</title>
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
      <div class="content-wrapper">
        <section class="content">
          <br>
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3"> 🚗 Travel Advance – 🤝 Meeting Overview 🚀</h3>
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
                          <form action="<?=base_url()?>Reports/AdvanceVSRPMeetingCountWithNextNewAdvance" method="post" class="mt-3">
                            <div class="row mb-4">
                              <?php /*
                              <div class="col">
                                <label for="selectedDate">Date</label>
                                <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 250px;" class="form-control">
                              </div>
                               <div class="col">
                                <label for="selectedDate">End Date</label>
                                <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                                </div>
                              */ ?>

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
                  <?php 
                    $userdetailsData                = $mdata['data1'];
                    $useActiveInActive              = $mdata['data2'];
                    $usePresent                     = $mdata['data3'];
                    $total_user                     = $useActiveInActive[0]->total_user;
                    $total_active_user              = $useActiveInActive[0]->total_active_user;
                    $total_inactive_user            = $useActiveInActive[0]->total_inactive_user;
                    $total_present                  = $usePresent[0]->total_present;
                    $total_work_from_Office         = $usePresent[0]->total_work_from_Office;
                    $total_work_from_field          = $usePresent[0]->total_work_from_field;
                    $total_work_from_field_Office   = $usePresent[0]->total_work_from_field_Office;
                    
                    ?>
                  <div class="card-body">
                    <hr>
                    <?php  // echo $user['user_id']; ?>
                    <div class="container-fluid body-content">
                      <div class="page-header">
                        <div class="form-group" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <fieldset>
                            <div class="table-responsive">
                              <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                   <tr>
                                    <th>#️⃣</th>
                                    <th>🆔 BD ID</th>
                                    <th>👤 BD Name</th>
                                    <th>🏢 Department</th>
                                    <th>🌐 Cluster Zone</th>
                                    <th>🌐 Total Travel Advance</th>
                                    <th>🌐 Total Expense Cash</th>
                                    <th>💰 User Available Amount</th>
                                    <th>📄 View Expense Details</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                        foreach($userdetailsData as $dt){
                                            $r = rand(150, 255);
                                            $g = rand(150, 255);
                                            $b = rand(150, 255);
                                            $backgroundColor = "rgba($r, $g, $b,.2)";
                                      
                                            $hue        = rand(0, 360); // Full color wheel
                                            $saturation = rand(60, 100); // High saturation for rich colors
                                            $lightness  = rand(30, 45); // Low lightness for a deeper tone
                                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                            if($dt->status == 'inactive'){
                                              continue;
                                            }
                                                                    
                                          ?>
                                    <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                                      <td><?=$i?></td>
                                      <td>
                                        <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserDetails/<?=$dt->user_id?>" target="_blank"><?=$dt->user_id?></a>
                                      </td>
                                      <td>
                                        <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserDetails/<?=$dt->user_id?>" target="_blank"><?=$dt->name?></a>
                                      </td>
                                      <td><?=$dt->user_roles?></td>
                                      <td><?=$dt->user_cluster_zone?></td>

                                      <td>
                                      <?php 
                                      $ocashreq               = $this->Report_model->GetAllTravelAdvancedByCodeSingleUser(8,$dt->user_id);
                                      $total_advance_by_user = 0;
                                        foreach ($ocashreq as $item) {
                                            $total_advance_by_user += $item->cash;
                                        }
                                        $total_advance_by_user_value =  $this->Report_model->format_inr_intl("$total_advance_by_user ₹");
                                        
                                      ?>
                                      <span style='color:green!important;'><?= $total_advance_by_user_value; ?></span>
                                      </td>

                                      <td>
                                      
                                      <?php 
                                        $totalExpenseDataByuser = $this->Report_model->GetTravelExpenseTotalSumBySingleUser($dt->user_id);
                                        $total_expense_by_user = 0;
                                        foreach ($totalExpenseDataByuser as $item) {
                                            $total_expense_by_user += $item->expense;
                                        }

                                        $total_expense_by_user_value =  $this->Report_model->format_inr_intl("$total_expense_by_user ₹"); 
                                        ?>
                                        <span style='color:red!important;'><?= $total_expense_by_user_value; ?></span>
                                      </td>

                                      <td> 
                                      <span style='color: #0a00c0ff !important;'>
                                        <?= $this->Report_model->format_inr_intl("$dt->ucash ₹"); ?>
                                      </span>
                                      </td>
                                      <td>
                                        <a style="background: navy;" target="_BLANK" href="<?=base_url()?>Reports/AdvanceVSRPMeetingCountWithNextNewAdvanceDetails/<?=$dt->user_id?>" class="btn btn-primary">View</a>
                                      </td>
                                    </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <!--END OF FORM ^^-->
                          </fieldset>
                        </div>
                        <hr />
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
    <script type='text/javascript'></script>
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
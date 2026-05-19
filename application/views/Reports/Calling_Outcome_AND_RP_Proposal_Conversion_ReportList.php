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
                  <div class="card-header text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">Calling Outcome & RP Proposal Conversion Report</h3>
                    <h5 class="text-center m-3"><?php $sdate = date('d-m-Y', strtotime($sdate)); echo "From Date: ".$sdate; ?> to <?php $edate = date('d-m-Y', strtotime($edate)); echo "To Date: ".$edate; ?></h5>
                    <h6 style="color:red;">
                        <?= $readable = ucwords(str_replace('_', ' ', $rtype)); ?>
                    </h6>
                    <h6>
                        <a style="color:navy;" style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$cid?>">
                            <?= htmlspecialchars($cmpDatas[0]->compname) ?>
                            (<?= htmlspecialchars($cid) ?>)
                         </a>
                    </h6>
                  </div>
                  <hr>
                </div>
              </div>
            </div>
            <hr>


                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                        <th class="text-capitalize">#️⃣ Sr. NO</th>
                          
                          <th>🏢 CID</th>
                          <th>🏢 Company</th>
                          <th>🏢 Company Current Status</th>
                          <th>👤 Main BD </th>
                          <th>👤 Task Name </th>
                          
                        
                          <?php if($rtype =='total_meeting'){?> 
                            <th>📌 Task Planned Status</th>
                            <th>📅 Appointment Date</th>
                            <th>🏷️ Meeting Type</th>
                            <th>📄 View MOM</th>
                            <th>✍️ Short MOM</th>
                            <th>👁️ View Details</th>

                          <?php } ?>
                          <?php if($rtype =='proposal_shared'){?> 
                            <th>📌 Task Planned Status</th>
                            <th>📅 Appointment Date</th>
                            <th>👁️ View Details</th>
                          <?php } ?>

                          <?php 
                            if (in_array($rtype, [
                                'total_planned_call_after_first_rp',
                                'total_call_connect_after_first_rp',
                                'total_planned_call_after_first_rp_by_mainbd',
                                'total_call_connect_after_first_rp_by_mainbd',
                                'total_planned_call_after_first_rp_by_acm',
                                'total_call_connect_after_first_rp_by_acm',
                                'total_planned_call_after_first_rp_by_cm',
                                'total_call_connect_after_first_rp_by_cm',
                                'total_planned_call_after_first_rp_by_pst',
                                'total_call_connect_after_first_rp_by_pst',
                                'total_planned_call_after_first_rp_by_rm',
                                'total_call_connect_after_first_rp_by_rm',
                                'total_planned_call_after_first_rp_by_admin',
                                'total_call_connect_after_first_rp_by_admin'
                            ])){
                            
                            ?> 
                          <th>🧑‍💼 Task Planned By</th>
                          <th>🏷️ Task Planner Name</th>
                          <th>📌 Task Planned Status</th>
                          <th>🔄 Task Update Status</th>
                          <th>📅 First Meeting Date</th>
                          <th>📞 Call After First RP</th>
                          <th>✅ Action Taken</th>
                          <th>🎯 Purpose Achieved</th>
                          <th>📝 Remarks</th>
                          <th>⭐ Special Remarks</th>

                          <?php } ?>

                          <?php if($rtype =='total_planned_call_after_first_proposal' || $rtype =='total_call_connect_after_first_proposal'){?> 
                            <th>🧑‍💼 Task Planned By</th>
                            <th>🏷️ Task Planner Name</th>
                            <th>📌 Task Planned Status</th>
                            <th>🔄 Task Update Status</th>
                            <th>📞 Call After First Proposal Date</th>
                            <th>✅ Action Taken</th>
                            <th>🎯 Purpose Achieved</th>
                            <th>📝 Remarks</th>
                            <th>⭐ Special Remarks</th>

                          <?php } ?>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($mdata as $row) {
                          $task_user_id     = $row->user_id;
                          $meeting_task_id  = $row->task_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="Task ID : <?= $meeting_task_id ?>">
                        <td><?= htmlspecialchars($i) ?></td>

                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>

                        <td><?= htmlspecialchars($row->current_status) ?></td>
                        <td><?= htmlspecialchars($row->mainbd_name) ?></td>
                        <td><?= htmlspecialchars($row->task_name) ?></td>

            

                        <?php if($rtype =='total_meeting'){?> 
                        <td><?= htmlspecialchars($row->task_planned_on_status) ?></td>
                        <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                        <td><?= htmlspecialchars($row->mtype) ?></td>
                        <td>
                          <?php 
                          $momtask_mom = '';
                          $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($meeting_task_id);
                          if(sizeof($momtaskDatas) > 0){
                             $momtask_id = $momtaskDatas[0]->id;
                             $momtask_mom = $momtaskDatas[0]->mom;
                             $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);
                             if(sizeof($momdatas) > 0){
                                $mom_id = $momdatas[0]->id;

                                echo '<a style="color:<?=$backgroundColorNew;?>!important" class="p-1 bg-success" target="_BLANK" href="' . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $row->cid . '">view</a>';
                                
                             }else{
                              echo "<span class='p-1 bg-warning'>Pending For Write</span>";
                             }
                          }else{
                            echo "NA";
                          }
                           ?>
                          </td>
                         <td><?= $momtask_mom; ?></td>
                         <td><a class="bg-success p-1" href="<?= base_url(); ?>Reports/ViewMeetingDetails/<?= $meeting_task_id; ?>" target="_blank">View Details</a></td>
                        <?php } ?>
                        
                         <?php if($rtype =='proposal_shared'){?> 
                          <td><?= htmlspecialchars($row->task_planned_on_status) ?></td>
                         <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                        
                         <td><a class="bg-success p-1" href="<?= base_url(); ?>Reports/ViewProposalDetails/<?= $row->proposal_id; ?>" target="_blank">View Details</a></td>
                        <?php } ?>

                        
                          <?php 
                     if (in_array($rtype, [
                        'total_planned_call_after_first_rp',
                        'total_call_connect_after_first_rp',
                        'total_planned_call_after_first_rp_by_mainbd',
                        'total_call_connect_after_first_rp_by_mainbd',
                        'total_planned_call_after_first_rp_by_acm',
                        'total_call_connect_after_first_rp_by_acm',
                        'total_planned_call_after_first_rp_by_cm',
                        'total_call_connect_after_first_rp_by_cm',
                        'total_planned_call_after_first_rp_by_pst',
                        'total_call_connect_after_first_rp_by_pst',
                        'total_planned_call_after_first_rp_by_rm',
                        'total_call_connect_after_first_rp_by_rm',
                        'total_planned_call_after_first_rp_by_admin',
                        'total_call_connect_after_first_rp_by_admin'
                    ])){
                            ?> 

                         
                          <td><?= htmlspecialchars($row->call_after_first_rp_task_by_name) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp_task_name) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp_task_planned_on_status) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp_update_on_status) ?></td>
                          <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp_actontaken) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp_purpose_achieved) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_rp_remarks) ?></td>
                            
                       <td><?php
                        if (
                            !empty($row->call_after_first_rp_special_remarks) &&
                            $row->call_after_first_rp_special_remarks !== null &&
                            $row->call_after_first_rp_special_remarks !== 'null' &&
                            $row->call_after_first_rp_special_remarks !== '[]'
                        ) {
                          
                          if($mtypes == 'total_special_remarks_complete_task'){
                           $jsonDatas = json_decode($row->call_after_first_rp_special_remarks, true);
                           foreach ($jsonDatas as $question => $answer) {
                                echo "<strong>$question</strong><br>Answer: $answer<hr>";
                            }
                          }else{ ?>

                          <button type="button" class="btn btn-primary"  onclick="SpecialRemarks(<?=$meeting_task_id ?>)">view</button>

                        <?php } ?>
                       <?php }else{
                          echo 'NA';
                        }
                    
                        ?></td>
                          <?php } ?>
                          
                          <?php if($rtype =='total_planned_call_after_first_proposal' || $rtype =='total_call_connect_after_first_proposal'){?> 

                         
                          <td><?= htmlspecialchars($row->call_after_first_proposal_task_by_name) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal_task_name) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal_task_planned_on_status) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal_update_on_status) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal_actontaken) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal_purpose_achieved) ?></td>
                          <td><?= htmlspecialchars($row->call_after_first_proposal_remarks) ?></td>
                            
                       <td><?php
                        if (
                            !empty($row->call_after_first_proposal_special_remarks) &&
                            $row->call_after_first_proposal_special_remarks !== null &&
                            $row->call_after_first_proposal_special_remarks !== 'null' &&
                            $row->call_after_first_proposal_special_remarks !== '[]'
                        ) {
                          
                          if($mtypes == 'total_special_remarks_complete_task'){
                           $jsonDatas = json_decode($row->call_after_first_proposal_special_remarks, true);
                           foreach ($jsonDatas as $question => $answer) {
                                echo "<strong>$question</strong><br>Answer: $answer<hr>";
                            }
                          }else{ ?>

                          <button type="button" class="btn btn-primary"  onclick="SpecialRemarks(<?=$meeting_task_id ?>)">view</button>

                        <?php } ?>
                       <?php }else{
                          echo 'NA';
                        }
                    
                        ?></td>
                          <?php } ?>
                         
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
                  <div class="modal fade" id="specialRemarksModal" tabindex="-1" role="dialog" aria-labelledby="specialRemarksTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="specialRemarksTitle">Special Remarks</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                      <div id="specialContent">

                                      </div>
                                  </div>
                                </div>
                              </div>
                            </div>
    <!-- /.container-fluid -->
    </section>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script>

         function SpecialRemarks(id){
              
                  $.ajax({
                  url:'<?=base_url();?>Menu/GetSpecialRemarksUsingTaskID',
                  type: "POST",
                  data: {
                  taskid: id
                  },
                  cache: false,
                  success: function a(result){
                    $('#specialRemarksModal').modal('show');
                    $("#specialContent").html(result);
                  }
                });
            }
       </script>
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
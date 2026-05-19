<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Daily Team Planner Summary Wwith Location Share | STEM APP | WebAPP</title>
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
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
   
        <?php   
        // foreach($usertaskData[0] as $key=>$ddd){
        //     echo $key;
        // }
        
        // dd($usertaskData[0]);
        
        ?>

        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center m-3 text-secondary">Daily Team Planner Summary With Location Share</h3>
                    <center>
                    Date - <?= $trrdate ?>
                    </center>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                            <div class="table-responsive">
                              <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead>
                                  <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Type Name</th>
                                    <th>Userworkfrom</th>
                                    <th>Total Task</th>
                                    <th>Approved Task</th>
                                    <th>Pending Approved</th>
                                    <th>Complete Task</th>
                                    <th>Pending Task</th>
                                    <th>Total Autotask</th>
                                    <th>After Task</th>
                                    <th>Complete Autotask</th>
                                    <th>Pending Autotask</th>
                                    <th>Action Yes Purpose Yes</th>
                                    <th>Action Yes Purpose No</th>
                                    <th>Action No Purpose No</th>
                                    <th>Call Task</th>
                                    <th>Email Task</th>
                                    <th>Scheduled Meeting Task</th>
                                    <th>Barg Meeting Task</th>
                                    <th>Whatsapp Activity</th>
                                    <th>Write Mom</th>
                                    <th>Write Proposal</th>
                                    <th>Research Task</th>
                                    <th>Documentation Task</th>
                                    <th>Social Networking Task</th>
                                    <th>Social Activity Task</th>
                                    <th>Join Meeting Task</th>
                                    <th>Mom Check Task</th>
                                    <th>Create Bd Request Task</th>
                                    <th>Submit Handover Task</th>
                                    <th>Praposal Check Task</th>                          
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php  $i=1; foreach ($usertaskData as $value): ?>
                                  <tr>
                                   
                                    <td><?=$i;?></td>
                                    <td><?= $value->name; ?></td>
                                    <td><?= $value->type_name; ?></td>
                                    <td><?= $value->userworkfrom; ?></td>
                                    <td><?= $value->total_task; ?></td>
                                    <td><?= $value->approved_task; ?></td>
                                    <td><?= $value->pending_approved; ?></td>
                                    <td><?= $value->complete_task; ?></td>
                                    <td><?= $value->pending_task; ?></td>
                                    <td><?= $value->total_autotask; ?></td>
                                    <td><?= $value->after_task; ?></td>
                                    <td><?= $value->complete_autotask; ?></td>
                                    <td><?= $value->pending_autotask; ?></td>
                                    <td><?= $value->action_yes_purpose_yes; ?></td>
                                    <td><?= $value->action_yes_purpose_no; ?></td>
                                    <td><?= $value->action_no_purpose_no; ?></td>
                                    <td><?= $value->call_task; ?></td>
                                    <td><?= $value->email_task; ?></td>
                                    <td><?= $value->scheduled_meeting_task; ?></td>
                                    <td><?= $value->barg_meeting_task; ?></td>
                                    <td><?= $value->whatsapp_activity; ?></td>
                                    <td><?= $value->write_mom; ?></td>
                                    <td><?= $value->write_proposal; ?></td>
                                    <td><?= $value->research_task; ?></td>
                                    <td><?= $value->documentation_task; ?></td>
                                    <td><?= $value->social_networking_task; ?></td>
                                    <td><?= $value->social_activity_task; ?></td>
                                    <td><?= $value->join_meeting_task; ?></td>
                                    <td><?= $value->mom_check_task; ?></td>
                                    <td><?= $value->create_bd_request_task; ?></td>
                                    <td><?= $value->submit_handover_task; ?></td>
                                    <td><?= $value->praposal_check_task; ?></td>

                                    
                                   
                                  </tr>
                                  <?php $i++; endforeach; ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                     
                            <hr>
                            <br>
                          
                            <div class="card p-2">
                            <center>
                            <form action="<?=base_url().'Menu/UpdateDailyTeamPlannerSummaryWithLocationShare'?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                                <?php  
                              $getSCTaskByTaskID = $this->Menu_model->GetSCTaskByTaskID($ctaskid);
                              $file_required = $getSCTaskByTaskID[0]->file_required;
                              ?>
                              <?php if($file_required == 'yes'){ ?> 
                                <div class="form-group">
                                  <label for="exampleFormControlTextarea1">Upload Report</label>
                                  <input type="file" name="filname[]" id="filname" class="form-control" multiple required>
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Add Remarks</label>
                                    <textarea required class="form-control" name="remarks" placeholder="* Please Add Daily Team Planner Summary With Location Remarks"  id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                                <div id="file-preview"></div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                            </center>
                            </div>



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
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
      
      
 $(document).ready(function () {
    $('#filname').on('change', function (event) {
        const files = event.target.files;
        const previewContainer = $('#file-preview');
        previewContainer.empty(); // Clear previous previews

        Array.from(files).forEach((file) => {
            const fileType = file.type;
            const fileReader = new FileReader();

            fileReader.onload = function (e) {
                if (fileType.startsWith('image/')) {
                    // Display image preview
                    previewContainer.append(
                        `<img src="${e.target.result}" alt="${file.name}" style="max-width: 200px; margin: 10px;">`
                    );
                } else if (fileType === 'application/pdf') {
                    // Display PDF preview
                    previewContainer.append(
                        `<div>
                            <embed src="${e.target.result}" type="application/pdf" width="300" height="200" />
                         </div>`
                    );
                } else {
                    previewContainer.append(
                        `<p>Unsupported file type: ${file.name}</p>`
                    );
                }
            };

            fileReader.readAsDataURL(file);
        });
    });
});
      
      
    </script>
  </body>
</html>
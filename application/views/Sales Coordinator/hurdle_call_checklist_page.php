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
      #file-preview img {
            display: block;
            margin: 5px 0;
        }

  #file-preview {
    text-align: center;
  }
  #file-preview {
    align-items: center;
    justify-content: center;
    display: flex;
    flex-wrap: wrap;
    background: #bdb76b42;
    padding: 10px;
}
.previewimg{
    box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
    padding: 10px;
}
textarea.form-control {
    box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px;
    outline: none;
    border: none;
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
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <section class="content">
          <div class="container-fluid">
          <?php if ($this->session->flashdata('pending_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('pending_message'); ?>
          </div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('success_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('success_message'); ?>
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
            <div class="row">
              <div class="col-12">
                
                  <?php if($ctaskid != ''){ ?>
                    <div class="card">
                    <?php 
                    $getSCTaskByTaskID  = $this->Menu_model->GetSCTaskByTaskID($ctaskid);
                    $file_required      = $getSCTaskByTaskID[0]->file_required;
                    $unique_kpa         = $getSCTaskByTaskID[0]->unique_kpa;
                    $type_of_task       = $getSCTaskByTaskID[0]->type_of_task;
                    $category           = $getSCTaskByTaskID[0]->category;
                    $appointment_datetime = $getSCTaskByTaskID[0]->appointment_datetime;
                    $initiated_datetime   = $getSCTaskByTaskID[0]->initiated_datetime;
                    $created_at           = $getSCTaskByTaskID[0]->created_at;
                    $given_time_task_time = $getSCTaskByTaskID[0]->task_time;
                    $fwd_date             = $getSCTaskByTaskID[0]->fwd_date;
                    $current_time         = date("H:i:s");
                    $appointment_date     = date("Y-m-d", strtotime($appointment_datetime));
                    $fwd_dates            = date("Y-m-d", strtotime($fwd_date));
                    if($appointment_date == $fwd_dates){
                      $otaskDate = "";
                    }else{
                      $otaskDate = "<h6 style='color:rgb(51, 0, 255) !important;' >Original Task Date = ".$fwd_date."</h6>";
                    }
                     
                    if(is_null($given_time_task_time) || $given_time_task_time == '00:00:00'){
                      $agttime = '';
                    }else{
                      $agttime = "<h6 style='color:rgb(51, 0, 255) !important;' >Task Time Defined by Admin = ".$given_time_task_time."</h6>";
                    }

                    $rqueryDatacnt = sizeof($rqueryData);
                    $request_status = $rqueryData[0]->status;
                    if($request_status == 1){
                      $screqueststatus = "<span style='color: rgb(51, 0, 255) !important;'>Your Request Approved Successfully.</span>";
                    }
                    ?>
                  <div class="card-header">
                    <h3 class="text-center m-3 text-capitalize" style="color: #ff00ac !important;"> <?= $unique_kpa; ?> </h3>
                    <hr style="width:450px">
                    <div class="text-center">
                        <?=$agttime;?>
                        <?=$otaskDate;?>
                        <h6> <span style="color: #ff00ac !important;">Todays Task Appointment Date : <?=$appointment_datetime;?></span></h6>
                        <h6> <span style="color:rgb(255, 72, 0) !important;">Task Initiate Time : <?=$initiated_datetime;?></span></h6>
                        <?=$screqueststatus;?>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="container body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                            <?php 
                            if ($current_time > $given_time_task_time && 
                            (!is_null($given_time_task_time) && $given_time_task_time !== '00:00:00') && 
                            ($request_status == 0 || $request_status == 2)) {
                         ?>
                                  <?php if($rqueryDatacnt == 0){ ?>
                                  <div class="text-center">
                                      <h4 class="text-center text-danger text-capitalize"> * The Task had to be submitted by the specified time of : <?=$given_time_task_time?></h4>
                                      <hr>
                                      <center>
                                          <form action="<?=base_url().'Menu/CreateRequestForScPerformTask'?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                                            <div class="form-group">
                                              <label for="exampleFormControlTextarea1" style="font-size:20px;">Type reason for late</label>
                                              <hr style="width:200px">
                                              <textarea required class="card p-2 form-control" name="reason_remarks" placeholder="* Please Type reason for late." rows="3" style="height:250px;"></textarea>
                                            </div>
                                            <hr>
                                            <button type="submit" class="btn btn-primary">Create Request</button>
                                          </form>
                                        </center>
                                    </div>
                                    <style>
                                      .btn.btn-primary{background: crimson; outline: navajowhite; border: navajowhite;}
                                      textarea.form-control{border: 1px solid red;}
                                    </style>
                                   <?php }else{ ?>
                                        <table class="table table-hover">
                                            <tbody>
                                              <tr class="table-primary">
                                                <th>Request Message</th>
                                                <td><?=$rqueryData[0]->remarks;?></td>
                                              </tr>
                                              <tr class="table-success">
                                                <th>Request Create Time</th>
                                                <td><?=$rqueryData[0]->created_at;?></td>
                                              </tr>
                                              <tr class="table-warning">
                                                <th>Request Status</th>
                                                <td><?php
                                                if($rqueryData[0]->status == 0){
                                                  echo "<span class='bg-warning p-1'>Pending</span>";
                                                }else if($rqueryData[0]->status == 1){
                                                  echo "<span class='bg-success p-1'>Approved</span>";
                                                }else if($rqueryData[0]->status == 2){
                                                  echo "<span class='bg-danger p-1'>Rejected</span>";
                                                }
                                                ?></td>
                                              </tr>
                                              <?php if($rqueryData[0]->status == 2){?>
                                                <tr class="table-success">
                                                <th>Reject Time</th>
                                                <td><?=$rqueryData[0]->apr_date;?></td>
                                              </tr>
                                                <tr class="table-success">
                                                <th>Reject Remarks</th>
                                                <td><?=$rqueryData[0]->apr_remarks;?></td>
                                              </tr>
                                               <?php } ?>
                                            </tbody>
                                          </table>
                                          <?php if($rqueryData[0]->status == 0){?>
                                          <div class="text-center">
                                                  <strong> * Please wait for approval. </strong>
                                          </div>
                                          <?php } ?>
                                   <?php }?>
                            <?php } else { ?>
                            <br>
                            <div class="card-body">
                              <div class="div text-right">
                              <button type="button" id="add-checklist" class="btn btn-success btn-sm">+ Add Checklist</button>
                              </div>
                <form action="<?=base_url().'Menu/'?>insert_hurdleCallData" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                <div id="checklist-container">
                    <?php 
              
                    foreach($hurdle_call_data as $key=>$val){
                    ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input question-checkbox" type="checkbox" id="<?php echo $val->question_name;?>" name="<?php echo $val->id;?>">
                            <label class="form-check-label" for="<?php echo $val->question_name;?>">
                            <?php echo $val->question_name;?>
                            </label>
                        </div>
                        <textarea class="form-control mt-2 remark-textarea" id="<?php echo $val->question_name;?>_remark" name="<?php echo $val->id;?>_remark" placeholder="Provide remarks for <?php echo $val->question_name;?>" required></textarea>
                    </div>
                    <?php }
                     ?>
                   
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
                        <div id="file-preview"></div>
                   </div>
                   <div class="form-group">
                      <label for="exampleFormControlTextarea1">Add Own Remarks</label>
                      <textarea required class="form-control" name="remarks" placeholder="* Please Add Daily Hurdle Call Checklist Remarks"  id="exampleFormControlTextarea1" rows="3"></textarea>
                  </div>
                 
                  <hr>
                                  <div id="file-preview"></div>
                                  <hr>
                                  </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-lg btn-primary btn-rounded shadow">Submit</button>
                    </div>
                </form>
               
            </div>
                            <?php } ?>
                          </fieldset>
                        </div>
                        <hr />
                      </div>
                    </div>
                  </div>
                </div>
                <?php }  ?>
              </div>
            </div>
          </div>
      </div>
    </div>
    </div>
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
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
                          `<img class="previewimg" src="${e.target.result}" alt="${file.name}" style="max-width: 250px; margin: 10px;">`
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

<script>
$(document).ready(function () {
    let checklistCount = 1;

    $("#add-checklist").click(function () {
        checklistCount++;
        let newChecklist = `
            <div class="mb-3 checklist-item">
                <div class="form-check">
                    <input class="form-check-input question-checkbox" type="checkbox" name="new_checklist[]">
                    <input type="text" class="form-control checklist-name" placeholder="Checklist name" name="new_checklist_remark_name[]" required>
                </div>
                <textarea class="form-control mt-2 remark-textarea" name="new_checklist_remark[]" placeholder="Provide remarks" required></textarea>
                <div class="text-right">
                <button type="button" class="btn btn-danger btn-sm remove-checklist">-</button></div>
            </div>`;
        $("#checklist-container").append(newChecklist);
    });

    $(document).on("click", ".remove-checklist", function () {
        $(this).closest(".checklist-item").remove();
    });
});
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
  </body>
</html>
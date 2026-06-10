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
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

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
  <?php //include 'addlog.php';?>
  <!-- /.navbar -->

  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
      <div class="container-fluid">
      <div id="create_act" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="modal-standard-title1"></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div> <!-- // END .modal-header -->
                      <div class="modal-body">
                          <div class="card row m-2">
                              <div class="col-12 col-md-12">
                                <?php
                                ?>
                        <div class="card-header bg-info" id="company-name"></div> <!-- Company name will be updated here -->
                        <div class="card-body">
                                      Current Status : <label id="current-status"></label><br> <!-- Current status will be updated here -->
                                      Last Task : <label id="last-task"></label><br> <!-- Last task will be updated here -->
                                      Last Task Remark : <label id="last-task-remark"></label> <!-- Last task remark will be updated here -->
                                  </div>
                              </div>
                          </div>
                          <div class="card card-form col-md-12">
                              <div class="row no-gutters">
                                  <div class="col-lg-12 card-form__body card-body">
                                      <center>
                                          <h5>Create Task</h5>
                                      </center>
                                      <hr>
                                      <?= form_open('Menu/addtaskNYA') ?>
                                      <input type="hidden" name="ntuid" value="<?= $uid ?>">
                                      <input type="hidden" id='ntinid' name="ntinid">
                                      
                                      <label for="assign">Assign Task</label>
                                      <input type="checkbox" id="assign" name="checkbox-name">
                                      <br>
                                      <div id="task-assign" style="display: none;">
                                        <lable>Select User</lable>
                                            <select id="user" name="user" class="form-control">
                                            <option value="">Select User</option>
                                            <?php $user = $this->Menu_model->get_adminWiseName($uid);
                                                foreach ($user as $a) { ?>
                                                    <option value="<?= $a->user_id?>"><?= $a->name ?></option>
                                                    <?php } ?>
                                            </select>
                                        <lable>Select Action</lable>
                                            <select id="action" name="action" class="form-control">
                                                <option value="">Select Action</option>
                                                <option value="1">Call</option>
                                                <option value="3">Scheduled Meeting</option>
                                                
                                            </select>

                                        <lable>Select Purpose</lable>
                                            <select id="ntppose" class="form-control" name="ntppose">
                                            </select>
                                            <lable>Select Next Action</lable>
                                            <select id="nextaction" class="form-control" name="ntnextaction">
                                            </select>
                                      </div>
                                      <div id="self-assign" style="display: block;">
                                        PST : <label id="pst"></label><br> <!-- Current status will be updated here -->
                                        Custer Manager : <label id="CM"></label><br>
                                        <?php $date = date('Y-m-d h:i:s'); ?>
                                        Date : <label id="date"><?=$date?></label><br>
                                        <!-- <lable>Current Status</lable> -->
                                        <input type="hidden" id="ntstatus" name="ntstatus" value="">
                                        <input type="hidden" id="ntaction" name="ntaction" value="1">
                                        <input type="hidden" id="ntdate" name="ntdate" value="<?=$date?>">
                                        Action : <label id="ntaction">CALL</label><br>
                                      </div>

                                      <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                                      </form>

                                  </div>

                              </div>
                          </div>
                      </div>
                  </div> <!-- // END .modal-body -->

              </div>
          </div>


        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Need Your Attention Companies</h3>
              </div>
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>Company Name</th>
                                            <th>Total Company Logs</th>
                                            <th>Partner Type</th>
                                            <th>Current_Status</th>
                                            <th>Same Status scince</th>
                                            <th>Days</th>
                                            <th>Topspender</th>
                                            <th>Potential Key Client</th>
                                            <th>Last_Task_Activity</th>
                                            <th>Current_Task_Activity</th>                                  
                                            <th>Action Taken</th>
                                            <th>Purpose Achieved</th>
                                            <th>Last_Task_Remarks</th>
                                            <th>Action</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=1;
                                        foreach($data as $md){
                                          $ltid = $md->ltid;
                                          $cid = $md->cid;
                                          $tid = $md->id;
                                          $mtd = $this->Menu_model->get_ccitblall($tid);
                                          $count=$this->Menu_model->get_taskCounts($cid);
                                          //dd($count);
                                          if($ltid!=''){    
                                            $mltd = $this->Menu_model->get_ccitblall($tid);
                                          }
                                        ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$md->bdname?></td>
                                        <td><a target="_blank" href="<?=base_url();?>Menu/companyDetails/<?=$md->cmpid_id?>"><?=$md->compname?></a></td>
                                        <td><a target="_blank" href="<?=base_url();?>Menu/companyTasklogs/<?=$md->cmpid_id?>"><?=$count[0]->records?></a></td>
                                        <td><?=$md->pname?></td>
                                        <td><?=$md->CurStatus?></td>
                                        <td><?=$md->updateddate?></td>
                                        <td><?=$md->days?></td>
                                        <td><?=$md->topspender?></td>
                                        <td><?=$md->pkclient?></td>
                                        <td><?php if($mltd!=''){echo $mltd[0]->current_action_type;}?></td>
                                        <td><?=$mtd[0]->current_action_type?></td>                                        
                                        <td><?=$md->actontaken?></td>
                                        <td><?=$md->purpose_achieved?></td>
                                        <td><?php if($mltd!=''){echo $mltd[0]->remarks;}?></td>
                                        <td><button type="button" class="btn btn-danger" onclick="addTask(<?= $md->cmpid_id?>)"> <b>+</b> Create Task</button>
                                        </td>
                                        
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>           
                </fieldset>
            </div>
            <hr />
        </div>
    </div></div></div></div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?=base_url();?>assets/js/dashboard.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
<script>

    function addTask(id) {
        // Show the modal
        $('#create_act').modal('show');

        // Send the `id` through an AJAX request to fetch company details
        $.ajax({
            url: "<?= base_url(); ?>Menu/fetchCompanyDetails", // Your API endpoint to get the details
            method: 'POST',
            data: { id: id }, // Send the id as data
            dataType: 'json',
            success: function(response) {
                // Update the modal fields with the fetched data
                $('#company-name').text(response.compname);
                $('#current-status').text(response.current_status);
                $('#ntstatus').val(response.current_status_id); // This sets the value for #ntstatus
                $('#ntinid').val(response.init_id); // This sets the value for #ntinid

                // Now that the values are set, you can safely run the second AJAX call
                var sid = $("#ntstatus").val();
                var inid = $("#ntinid").val();
                var aid = document.getElementById("ntaction").value;

                console.log('status and action');
                console.log(sid);
                console.log(aid);
                console.log(inid);

                $.ajax({
                    url: '<?= base_url(); ?>Menu/getCMandPST',
                    type: "POST",
                    data: {
                        inid: inid
                    },
                    cache: false,
                    success: function(result) {
                        $('#CM').text(response.CM);
                        $('#pst').text(response.PST);
                    },
                    error: function(xhr, status, error) {
                        console.log("Error in getpurpose AJAX: " + error);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.log("Error in fetchCompanyDetails AJAX: " + error);
            }
        });

    //});

    $('#action').on('change', function f() {
        var sid = document.getElementById("ntstatus").value;
        var aid = document.getElementById("action").value;
        console.log("inside on change");
        console.log(sid);
        console.log(aid);
        $.ajax({
            url: '<?= base_url(); ?>Menu/getpurpose',
            type: "POST",
            data: {
                sid: sid,
                aid: aid
            },
            cache: false,
            success: function a(result) {
                $("#ntppose").html(result);
            }
        });
    });

    $('#ntppose').on('change', function f() {
        var pid = document.getElementById("ntppose").value;
        console.log(pid)
        $.ajax({
            url: '<?= base_url(); ?>Menu/getnextaction',
            type: "POST",
            data: {
                pid: pid
            },
            cache: false,
            success: function a(result) {
                $("#nextaction").html(result);
            }
        });
    });

    $('#nextaction').on('change', function f() {
        var action_id = document.getElementById("nextaction").value;

        $.ajax({
            url: '<?= base_url(); ?>Menu/purpose',
            type: "POST",
            data: {
                action_id: action_id
            },
            cache: false,
            success: function a(result) {
                $("#nextpurpose").html(result);
            }
        });
    });

    $('#nextpurpose').on('change', function g() {
        var purpose_id = document.getElementById("nextpurpose").value;

        $.ajax({
            url: '<?= base_url(); ?>Menu/actionremark',
            type: "POST",
            data: {
                purpose_id: purpose_id
            },
            cache: false,
            success: function a(result) {
                $("#next_action_remark").html(result);
            }
        });
    });

    $('#next_action_remark').on('change', function c() {
        var ab = this.value;
        document.getElementById("next_action_remark_msg").value = ab;
    });

    }
</script>
<script>
document.getElementById('assign').addEventListener('change', function() {
    if (this.checked) {
        // Show the "Assign Task" div and hide the "Self Assign" div
        document.getElementById('task-assign').style.display = 'block';
        document.getElementById('self-assign').style.display = 'none';
    } else {
        // Hide the "Assign Task" div and show the "Self Assign" div
        document.getElementById('task-assign').style.display = 'none';
        document.getElementById('self-assign').style.display = 'block';
    }
});
</script>

</body>
</html>
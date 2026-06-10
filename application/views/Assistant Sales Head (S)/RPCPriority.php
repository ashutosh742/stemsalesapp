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


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Report</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <fieldset>
                         <div class="table-responsive">
                            <div class="table-responsive">    
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>PST Name</th>
                                            <th>Company Name</th>
                                            <th>Last_Task Date</th>
                                            <th>Current_Task Date</th>
                                            <th>Next_Task Date</th>
                                            <th>Current task planned after time difference</th>
                                            <th>Next task planned after time difference</th>	
                                            <th>UpdateDate</th>
                                            <th>Last_Task_Activity</th>
                                            <th>Current_Task_Activity</th>
                                            <th>Next_Task_Activity</th>
                                            <th>Last_Task_Remarks</th>
                                            <th>Current_Task_Remarks</th>
                                            <th>Next_Task_Planned</th>
                                            <th>Current_Task_Purpose</th>
                                            <th>Current Purpose Achieved</th>
                                            <th>Next_Task_Purpose</th>
                                            <th>Last_Status</th>
                                            <th>Current_Status</th>
                                            <th>Last_ActionTaken</th>
                                            <th>Current_ActionTaken</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1;
                                        foreach($mdata as $md){
                                            $tid = $md->id;
                                            $inid  = $md->cid_id;
                                            $inid = $this->Menu_model->get_initbyid($inid);
                                            $mtd = $this->Menu_model->get_ccitblall($tid);
                                            if($mtd[0]->priority==''){
                                            $ltime = $mtd[0]->last_task_date;
                                            $ctime = $mtd[0]->appointmentdatetime;
                                            $ntime = $mtd[0]->next_task_date;
                                            if($ntime==''){$tdiff='';}else{$tdiff = $this->Menu_model->timediff($ctime,$ntime);}
                                            $nltime = date('d-m-Y h:i A', strtotime($ltime));
                                            $nctime = date('d-m-Y h:i A', strtotime($ctime));
                                            $nntime = date('d-m-Y h:i A', strtotime($ntime));
                                            $pst = $inid[0]->apst;
                                            if($pst){$pstname=$this->Menu_model->get_userbyid($pst);$pstname=$pstname[0]->name;}else
                                            {$pstname='';}
                                        ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$user['name']?></td>
                                        <td><?=$pstname?></td>
                                        <td>
                                            <button id="RPP<?=$i?>" value="<?=$tid?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                                                <?=$mtd[0]->compname?>
                                            </button>
                                        </td>
                                        <td><?=$nltime?></td>
                                        <td><?=$nctime?></td>
                                        <td><?=$nntime?></td>
                                        <td><?=$this->Menu_model->timediff($ltime,$ctime)?></td>
                                        <td><?=$tdiff?></td>
                                        <td><?=$mtd[0]->fwd_date?></td>
                                        <td><?=$mtd[0]->last_task_activity?></td>
                                        <td><?=$mtd[0]->current_action_type?></td>
                                        <td><?=$mtd[0]->next_task_activity?></td>
                                        <td><?=$mtd[0]->remarks?></td>
                                        <td><?=$mtd[0]->next_remarks?></td>
                                        <th><?=$mtd[0]->nextaction?></td>
                                        <td><?=$mtd[0]->last_purpose?></td>
                                        <td>NA</td>
                                        <td><?=$mtd[0]->next_purpose?></td>
                                        <td><?=$mtd[0]->last_status?></td>
                                        <td><?=$mtd[0]->current_status?></td>
                                        <td><?=$mtd[0]->last_action_taken?></td>
                                        <td><?=$mtd[0]->actontaken?></td>
                                    <?php $i++;}} ?>
                                  </tbody>
                                </table> 
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
            
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
    
<div id="show_RPP" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                      <?php $mtd = $this->Menu_model->get_ccitblall($tid);?>
                    <?=form_open('Menu/tsetpriority')?>
                    <div class="m-2 p-2"><input type="hidden" id="tmid" name="tmid" class="form-control" readonly>
                    <input type="text" id="cname" class="form-control" readonly></div>
                    <div class="m-2 p-2"><textarea type="text" id="ctmom" class="form-control" rows="10" readonly></textarea></div>
                    <div class="m-2 p-2"><lable>Select Priority Calling</lable>
                    <select class="form-control" name="priority">
                        <option value="yes">Priority</option>
                        <option value="no">Less Priority</option>
                    </select></div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                    </div>
                    </form>
                </div>
            </div> <!-- // END .modal-body -->
                
        </div>
    </div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">   
$('[id^="RPP"]').on('click', function() {
    $('#show_RPP').modal('show');
    var tid = this.value;
        $.ajax({
         url:'<?=base_url();?>Menu/ccitblall',
         method: 'post',
         data: {tid: tid},
         dataType: 'json',
         success: function(response){
           var len = response.length;
           $('#id,#compname,#mom').text('');
           if(len > 0){
             var id = response[0].id;
             var compname = response[0].compname;
             var mom = response[0].mom;
             document.getElementById("tmid").value = id;
             document.getElementById("cname").value = compname;
             document.getElementById("ctmom").value = mom;
           }
         }
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

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
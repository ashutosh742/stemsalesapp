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
            <h1 class="m-0">Review Report</h1>
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
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <fieldset>
                
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <div class="pdf-viwer">
                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <tr>
                                            <th>S.No.</th>
                                            <th>Date</th>
                                            <th>BD Name</th>
                                            <th>Client Name</th>
                                            <th>Remark</th>
                                            <th>Assign Task</th>
                                            <th>Expacted Date</th>
                                            <th>Expacted Status</th>
                                            <th>Review Time Status</th>
                                            <th>Current Status</th>
                                            <th>Potential</th>
                                            <th>Call Remark</th>
                                            <th>Email Remark</th>
                                            <th>Meeting Remark</th>
                                            <th>Edit</th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1;
                                        foreach($mdata as $md){
                                            $inid = $md->inid;
                                            $pstid = $this->Menu_model->get_initbyid($inid);
                                            $pstid = $pstid[0]->apst;
                                            
                                            $sdatet=$md->sdatet;
                                            $sdatet = date('d-m-Y  h:i A', strtotime($sdatet));
                                            if($md->potential==''){
                                        ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$sdatet?></td>
                                        <td><?=$md->bdname?></td>
                                        <td><?=$md->cmpname?></td>
                                        <td><?=$md->remark?></td>
                                        <td><?=$md->aname?></td>
                                        <td><?=$md->exdate?></td>
                                        <td><?=$md->exname?></td>
                                        <td><?=$md->rtsname?></td>
                                        <td><?=$md->csname?></td>
                                        <td><?=$md->potential?></td>
                                        <td><?=$md->ans1?></td>
                                        <td><?=$md->ans2?></td>
                                        <td><?=$md->ans3?></td>
                                        <td><button type="button" id="add_Rremark<?=$i?>" value="<?=$md->rid?>">Click</button></td>
                                    </tr></a>
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
    
<div id="doaction" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <div class="card card-form col-md-12">
                       <div class="card-header bg-info">Edit Review Remark</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/uprremark')?>
                           <input type="hidden" name="rid" id="rid">
                           <div id="orrr">
                           <lable>Potential / Non-Potential Clients? </lable>
                           <select class="form-control" id="potential" name="potential">
                               <option value='yes'>Potential</option>
                               <option value='no'>Non-Potential</option>
                           </select>
                           
                           <lable>1st Remark</lable>
                           <select class="form-control" id="otherremark">
                                <option>Select Option</option>
                                <option>No Calling Done</option>
                                <option>RP Changed</option>
                                <option>Never connected on a call</option>
                                <option>Transfer to Mam (Connect at HO)</option>
                                <option>ReMeeting required</option>
                                <option>Budget Exhausted/Allocated</option>
                                <option>Different CSR theme</option>
                                <option>No Meeting Done</option>
                                <option>Other</option>
                           </select>
                           <input type="text" class="form-control" name="otherremark" id="otherremark1">
                           
                           <lable>Call Remark</lable>
                           <select class="form-control" id="ans1">
                                <option>Select Option</option>
                                <option>Tried Calling Once but was not reached</option>
                                <option>Tried Calling Twice but was not reached</option>
                                <option>Tried Calling Thrice but was not reached</option>
                                <option>Tried Calling a More then three time but was not reached</option>
                                <option>Not Connected on Call</option>
                                <option>Followup Call Done Once but Meeting not Scheduled</option>
                                <option>Followup Call Done Twice but Meeting not Scheduled</option>
                                <option>Followup Call Done More Than Three Times but Meeting not Scheduled</option>
                                <option>Other</option>
                           </select>
                           <input type="text" class="form-control" name="ans1" id="ans11">
                           
                           <lable>Email Remark</lable>
                           <select class="form-control" id="ans2">
                               <option>Select Option</option>
                                <option>No Mail done</option>
                                <option>Mailed Once</option>
                                <option>Mailed Twice</option>
                                <option>Mailed more than Twice</option>
                                <option>Other</option>
                           </select>
                           <input type="text" class="form-control" name="ans2" id="ans21">
                           
                           <lable>Meeting Remark</lable>
                           <select class="form-control" id="ans3">
                                    <option>Select Option</option>
                                    <option>No Barge in Done</option>
                                    <option>Barge in done Once but No Purpose Achieved</option>
                                    <option>Barge in done Twice but No Purpose Achieved</option>
                                    <option>Barge in done more than twice but No Purpose Achieved</option>
                                    <option>Other</option>
                           </select>
                           <input type="text" class="form-control" name="ans3" id="ans31">
                           
                           <lable>Add CSR Budget</lable>
                           <select class="form-control" id="csrbudget" name="csrbudget">
                               <option>More than 2.5 csr budget</option>
                                <option>Between 50 lacs to 2 cr</option>
                                <option>Less than 50 lacs</option>
                           </select>
                           <lable>Add Number of schools</lable>
                           <input type="number" class="form-control" name="bdscholl" id="bdscholl">
                       
                       </div>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
$('[id^="add_Rremark"]').on('click', function() {
    var rid = this.value;
    $('#doaction').modal('show');
    document.getElementById("rid").value = rid;
});

$('#otherremark').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("otherremark1").readOnly = false;}else{
    document.getElementById("otherremark1").value=val;document.getElementById("otherremark1").readOnly = true;}
});

$('#ans1').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("ans11").readOnly = false;}else{
    document.getElementById("ans11").value=val;document.getElementById("ans11").readOnly = true;}
});

$('#ans2').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("ans21").readOnly = false;}else{
    document.getElementById("ans21").value=val;document.getElementById("ans21").readOnly = true;}
});

$('#ans3').on('change', function b() {
    var val = this.value;
    if(val=='Other'){document.getElementById("ans31").readOnly = false;}else{
    document.getElementById("ans31").value=val;document.getElementById("ans31").readOnly = true;}
});

$('#csrbudget').on('change', function b() {
    var val = this.value;
    if(val=='More than 2.5 csr budget'){
        var bdscholl = document.getElementById("bdscholl");
        bdscholl.min = 5;
        bdscholl.max = 10;
    }
    if(val=='Between 50 lacs to 2 cr'){
        var bdscholl = document.getElementById("bdscholl");
        bdscholl.min = 2;
        bdscholl.max = 5;
        
    }
    if(val=='Less than 50 lacs'){
        var bdscholl = document.getElementById("bdscholl");
        bdscholl.min = 1;
        bdscholl.max = 2;
        
    }
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
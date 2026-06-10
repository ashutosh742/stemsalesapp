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
                <h3 class="card-title"></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Company name
                                            <th>Updated By</th>
                                            <th>Planned Date</th>
                                            <th>Updated Date</th>
                                            <th>Current Action</th>
                                            <th>Action Taken</th>
                                            <th>Purpose Achieved</th>
                                            <th>Remarks</th>
                                            <th>MOM</th>
                                            <th>Last Status</th>
                                            <th>Current Status</th>
                                            <th>Next Status</th>
                                            <th>Review</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php 
                                        $i=1;
                                        foreach($mdata as $logs){
                                            $bdid = $logs->user_id;
                                            $aid = $logs->actiontype_id;
                                            $ui=$this->Menu_model->get_userbyid($bdid);
                                            $ai=$this->Menu_model->get_actionbyid($aid);
                                        ?>
                                    <tr>
                                            <td><?=$i?></td>            
                                            <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$logs->cmpid?>" target="_blank"><?=$logs->compname?></a></td>
                                            <td><?=$ui[0]->name?></td>
                                            <td><?=$logs->appointmentdatetime?></td>
                                            <td><?=$logs->updateddate?></td>
                                            <td><?=$ai[0]->name?></td>
                                            <td><?=$logs->actontaken?></td>
                                            <td><?=$logs->purpose_achieved?></td>
                                            <td><?=$logs->remarks?></td>
                                            <td><?=$logs->mom?></td>
                                            <td><?=$logs->lstid?></td>
                                            <td><?=$logs->tasksid?></td>
                                            <td><?=$logs->nstid?></td>
                                            <th><button type="button" id="add_Rremark<?=$i?>" value="<?=$logs->cmpid?>">Click</button></th>
                                     </tr>
                                     <?php $i++;} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
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
                        
                       <div class="card-header bg-info">Add Review Remark</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/rtaskc')?>
                           
                           <div class="was-validated m-3">
                            <div class="form-group">
                                <textarea class="form-control" name="remark" placeholder="Review Remark..."  required=""></textarea>
                            </div>
                            <hr>
                           <input type="hidden" name="pstuid" value="<?=$uid?>">
                           <input type="hidden" name="bduid" value="<?=$bdid?>">
                           <input type="hidden" id="rid" name="rid" value="<?=$rid?>">
                           <input type="hidden" id="cid" name="cid" >
                           
                           <center><h5>Create Task</h5></center>
                           <input type="hidden" name="ntuid" value="<?=$uid?>">
                           <input type="hidden" name="ntinid" value="">
                           <input type="datetime-local" name="ntdate" value="<?=date('Y-m-d H:i:s');?>" class="form-control" required="">
                           <lable>Current Status</lable>
                           <input type="hidden" id="ntstatus" name="ntstatus" value="8">
                          
                           
                           <lable>Select Action</lable>
                           <select id="ntaction" name="ntaction" class="form-control">
                               <option value="">Select Action</option>
                               <?php $action = $this->Menu_model->get_action();
                               foreach($action as $a){?>
                                   <option value="<?=$a->id?>"><?=$a->name?></option>
                               <?php } ?>
                           </select>
                           <lable>Select Purpose</lable>
                           <select id="ntppose" class="form-control" name="ntppose">
                           </select>
                           
                           <lable>Expected Status</lable>
                           <select class="form-control" id="exsid" name="exsid" required="">
                                <option value="">Select Status</option>
                                <?php $status = $this->Menu_model->get_status($uid);
                                foreach($status as $st){
                                ?>
                                <option value="<?=$st->id?>"><?=$st->name?></option>
                                <?php } ?>
                            </select>
                            
                            <lable>Expected Date</lable>
                            <input type="date" name="exdate" value="" class="form-control" required="" min="<?=date('Y-m-d');?>">
                            <div class="form-group text-center mt-3">
                                <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Submit</button>
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
    var cmpid = this.value;
    $('#doaction').modal('show');
    $.ajax({
     url:'<?=base_url();?>Menu/inidbycid',
     method: 'post',
     data: {cmpid: cmpid},
     dataType: 'json',
     success: function(response){
       var len = response.length;
       $('#inid').text('');
       if(len > 0){
         var inid = response[0].id;
         document.getElementById("cid").value = inid;
       }
 
     }
   });
});




$('#ntaction').on('change', function f() {
var sid = document.getElementById("ntstatus").value;
var aid = document.getElementById("ntaction").value;
    $.ajax({
    url:'<?=base_url();?>Menu/getpurpose',
    type: "POST",
    data: {
    sid: sid,
    aid: aid
    },
    cache: false,
    success: function a(result){
    $("#ntppose").html(result);
    }
    });
});

$('#ntppose').on('change', function f() {
    var pid = document.getElementById("ntppose").value;
$.ajax({
url:'<?=base_url();?>Menu/getnextaction',
type: "POST",
data: {
pid: pid
},
cache: false,
success: function a(result){
$("#nextaction").html(result);
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

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
            <h1 class="m-0">Total Request</h1>
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
                <h3 class="card-title">Total Request</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                        <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Request ID</th>
                                            <th>Request Date</th>
                                            <th>BD Name</th>
                                            <th>Request Type</th>
                                            <th>Remark</th>
                                            <th>Client Name</th>
                                            <th>Last Update Date</th>
                                            <th>Last Update Remark</th>
                                            <th>Next Action</th>
                                            <th>Attachment</th>
                                            <th>Total Logs</th>
                                            <th>Logs Detail</th>
                                            <th>Comment/Closed/Attachment</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=1;
                                        $bd = $this->Menu_model->get_userbyaid('45');
                                        foreach ($bd as $b){
                                        $bdid = $b->user_id;
                                        $mdata=$this->Menu_model->get_bdrequest($bdid,$code);
                                        foreach($mdata as $dt){
                                            $tid = $dt->id;
                                            $logs=$this->Menu_model->get_bdrequestlog($tid);
                                            $attech=$this->Menu_model->get_bdrequestattech($tid);
                                            $j = sizeof($logs);
                                            $j = $j-1;
                                            if($dt->assignto=='0'){$txt="Waiting for Admin Approval";}
                                            elseif($dt->assignstatus=='0'){$txt="Waiting for Request Assign to Concerned Person";}
                                            elseif($dt->assignstatus=='1'){$txt="Waiting for Request Close by Concerned Person";}
                                            elseif($dt->assignstatus=='2'){$txt="Waiting for Request Close by Request Creator";}
                                            else{$txt="All Done";}?>
                                      <tr>
                                         <td><?=$i?></th>
                                         <td><?=date('Ymd')?></th>
                                         <td><?=$dt->sdatet?></td>
                                         <td><?=$dt->bd_name?></td>
                                         <td><?=$dt->request_type?></td>
                                         <td><?=$dt->remark?></td>
                                         <td><?=$dt->cname?></td>
                                         <td><?php if($logs){echo $logs[$j]->sdatet;}?></td>
                                         <td><?php if($logs){echo $logs[$j]->detail;}?></td>
                                         <td><?=$txt?></td>
                                         <td><?php if($attech[0]->att==''){echo 'Not Available';}
                                         else{
                                            $atte = $attech[0]->att;
                                            $atte = preg_split ("/\,/", $atte);
                                            $l=sizeof($atte);
                                            for($k=1;$k<$l;$k++){
                                            if($atte[$k]!=''){
                                            ?>
                                            <a href="https://stemoppapp.in/<?=$atte[$k]?>" target="_blank"><p>Download</p></a>
                                            <?php }}}?>
                                         </td>
                                         <th><a href="<?=base_url();?>Menu/bdrequestlogs/<?=$tid?>"> Total <?=sizeof($logs)?> Logs </a></th>
                                         <td><?php $olddate=''; foreach($logs as $log){
                                         if($olddate==''){$olddate=$log->sdatet;}
                                         $newddate = $log->sdatet;
                                         $timed = $this->Menu_model->timediff($newddate, $olddate);
                                         ?>
                                             <b><?=is_numeric($log->tby)?$log->fullname:$log->tby?></b><br><?=$log->sdatet?><br>
                                             <b><?=$timed?></b><br><b>Detail:</b> <?=$log->detail?><hr>
                                         <?php $olddate=$newddate;} ?></td>
                                         <td><?php $st = $dt->status; if($st==0){?>
                                             
                                             <button id="add_comment<?=$i?>" value="<?=$tid?>">Comment</button><br><br>
                                             <button id="add_close<?=$i?>" value="<?=$tid?>">Close</button>
                                             <?php } else{echo 'Request Cloesd';}?></td>
                                                                                          
                                     </tr>
                                     <?php $i++;}} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>           <!--END OF FORM ^^-->
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
<div id="dcompany" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Add Delete Remark</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/dremark')?>
                           <input type="hidden" name="codeid" value="<?=$code?>">
                           <input type="hidden" name="bdid" value="<?=$bdid?>">
                           <input type="hidden" name="cid" id="cmpida">
                           <input type="hidden" name="pstuid" value="<?=$uid?>">
                           <textarea rows="10" name="dremark" class="form-control"></textarea><hr>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>

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
                           <?=form_open('Menu/rremark')?>
                           
                           <input type="hidden" name="codeid" value="<?=$code?>">
                           <input type="hidden" name="cid" id="cmpid">
                           <input type="hidden" name="pstuid" value="<?=$uid?>">
                           <textarea rows="10" name="remark" class="form-control"></textarea><hr>
                           
                           <center><h5>Create Task</h5></center>
                           <input type="hidden" name="ntuid" value="<?=$uid?>">
                           <input type="hidden" name="ntinid" value="<?=$init[0]->id?>">
                           <?php $date = date('Y-m-d h:i:s');?>
                           <input type="datetime-local" name="ntdate" value="<?=$date?>" class="form-control">
                           <lable>Current Status</lable>
                           <input type="hidden" id="ntstatus" name="ntstatus" value="8">
                           <lable>Select Task for</lable>
                           <select class="form-control" name="bdid">
                               <option value="<?=$uid?>"><?=$user['name']?></option>
                           </select>
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
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>


<div id="comment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Add Comment</div>
                        <div class="col-lg-12 card-body">
                            <form action="<?=base_url();?>Menu/bdrcomment" method="POST" enctype="multipart/form-data">
                           <input type="hidden" id="rid" name="rid">
                           <textarea rows="10" name="rcomment" class="form-control"></textarea><hr>
                           <input type="file" class="form-control-file" name="filname"><br>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>

<div id="bdrclose" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Close Request</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/bdrclose')?>
                           <input type="hidden" id="rrid" name="rrid">
                           <textarea rows="10" name="ccomment" class="form-control"></textarea><hr>
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
    document.getElementById("cmpid").value = cmpid;
});


$('[id^="DeleteC"]').on('click', function() {
        var cmpid = this.value;
    $('#dcompany').modal('show');
    document.getElementById("cmpida").value = cmpid;
});


$('[id^="add_comment"]').on('click', function() {
        var rid = this.value;
    $('#comment').modal('show');
    document.getElementById("rid").value = rid;
});


$('[id^="add_close"]').on('click', function() {
        var rrid = this.value;
    $('#bdrclose').modal('show');
    document.getElementById("rrid").value = rrid;
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

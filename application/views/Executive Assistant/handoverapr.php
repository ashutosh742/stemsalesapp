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
                  <div class="body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <input type="hidden" id="uuid" value="<?=$uid?>">
                        <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                              <th>SNo</th>
                                              <th>Client Name</th>
                                              <th>Mediator</th>
                                              <th>No of School</th>
                                              <th>Location</th>
                                              <th>City</th>
                                              <th>State</th>
                                              <th>SPD Identify By</th>
                                              <th>Infrastructure</th>
                                              <th>Logo</th>
                                              <th>Contact Person</th>
                                              <th>Mobile No</th>
                                              <th>Language</th>
                                              <th>Ex Installation Date</th>
                                              <th>Project Tenure</th>
                                              <th>Work Order No</th>
                                              <th>Total Budget</th>
                                              <th>PAN NO</th>
                                              <th>GST NO</th>
                                              <th>Remark</th>
                                              <th>Payment Term</th>
                                              <th>Other Remark</th>
                                              <th>MOU Date</th>
                                              <th>MOU</th>
                                              <th>Approval</th>
                                              <th>Edit</th>
                                              <th>Reject</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php 
                                        $bds=[];
                                        $j=0;
                                           $i=1; 
                                        foreach($bdid as $bd){
                                            $bds[$j]=$bd->user_id;
                                        $j++;}
                                        
                                      
                                      
                                        foreach($client as $d){
                                             
                                          $cid = $d->cid;
                                          if (in_array($d->bd_id, $bds))
                                          {
                                             
                                      ?>
                                      
                                    <tr>
                                        <td><input type="hidden" name="id[]" value="<?=$i?>"><?=$i?></td>
                                        <td><input type="hidden" name="client_name[]" value="<?=$d->client_name?>"><?=$d->client_name?></td>
                                        <td><input type="hidden" name="mediator[]" value="<?=$d->mediator?>"><?=$d->mediator?></td>
                                        <td><input type="hidden" name="noofschool[]" value="<?=$d->noofschool?>"><a href="spddetail/<?=$cid?>"><?=$d->noofschool?></a></td>
                                        <td><input type="hidden" name="location[]" value="<?=$d->location?>"><?=$d->location?></td>
                                        <td><input type="hidden" name="city[]" value="<?=$d->id?>"><?=$d->city?></td>
                                        <td><input type="hidden" name="state[]" value="<?=$d->id?>"><?=$d->state?></td>
                                        <td><input type="hidden" name="spd_identify_by[]" value="<?=$d->spd_identify_by?>"><?=$d->spd_identify_by?></td>
                                        <td><input type="hidden" name="infrastructure[]" value="<?=$d->infrastructure?>"><?=$d->infrastructure?></td>
                                        <td><input type="hidden" name="logo[]" value="<?=$d->logo?>">
                                        
                                        <?php $logo = $d->logo; $logo = preg_split ("/\,/", $logo);
                                                $l=sizeof($logo);
                                                for($i=0;$i<$l;$i++){?>
                                            
                                            <a href="../<?=$logo[$i]?>" target="_blank">LOGO</a>
                                            <?php } ?>
                                        
                                        </td>
                                        <td><input type="hidden" name="contact_person[]" value="<?=$d->contact_person?>"><?=$d->contact_person?></td>
                                        <td><input type="hidden" name="cp_mno[]" value="<?=$d->cp_mno?>"><?=$d->cp_mno?></td>
                                        <td><input type="hidden" name="language[]" value="<?=$d->language?>"><?=$d->language?></td>
                                        <td><input type="hidden" name="expected_installation_date[]" value="<?=$d->expected_installation_date?>"><?=$d->expected_installation_date?></td>
                                        <td><input type="hidden" name="project_tenure[]" value="<?=$d->project_tenure?>"><?=$d->project_tenure?></td>
                                        <td><?=$d->wono?></td>
                                        <td><?=$d->tbudget?></td>
                                        <td><?=$d->panno?></td>
                                        <td><?=$d->gstno?></td>
                                        <td><?=$d->remark?></td>
                                        <td><?=$d->payterm?></td>
                                        <td><?=$d->srfinovice?></td>
                                        <td><?=$d->moudate?></td>
                                        <td>
                                            <?php $mou = $d->mou; $mou = preg_split ("/\,/", $mou);
                                                $l=sizeof($mou);
                                                for($i=0;$i<$l;$i++){?>
                                            
                                            <a href="../<?=$mou[$i]?>" target="_blank">MOU</a>
                                            <?php } ?>
                                        
                                        
                                        </td>
                                        <th><a href="handApr/<?=$cid?>"><b>Approval</b></a></th>
                                        <th><a href="handEdit/<?=$cid?>"><b>Edit</b></a></th>
                                        <th><a href="handDelete/<?=$cid?>"><b>Reject</b></a></th>
                                    </tr>
                                    <?php $i++; }} ?>
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
                       <div class="card-header bg-info">Delete Handover</div>
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

<div id="aprh" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Approval Handover</div>
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
                       <div class="card-header bg-info">Delete Handover</div>
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">

function Apr(aprid) {
document.getElementById("cmpida").value = cmpid;
$.ajax({
url:'<?=base_url();?>Menu/handApr',
type: "POST",
data: {
aprid: aprid,
admid: admid
},
cache: false,
success: function b(result){
$("#cinfo").html(result);
}
});
}

$('[id^="Edit"]').on('click', function() {
        var cmpid = this.value;
    $('#edith').modal('show');
    document.getElementById("cmpida").value = cmpid;
});
$('[id^="Delete"]').on('click', function() {
        var cmpid = this.value;
    $('#deleteh').modal('show');
    document.getElementById("cmpida").value = cmpid;
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
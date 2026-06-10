<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM Operation | WebAPP</title>

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
            <h4>Roster Calling</h4>
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
       <div class="row p-3">
           <div class="col-sm col-md-6 col-lg-6 m-auto">
              <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <?php 
                     $ro = $this->Menu_model->get_rostsed($uid);
                     if($ro){$bdid=$ro[0]->uid;} else{$bdid=0;}
                     if($bdid==0){
                  ?>
                  <form action="<?=base_url();?>Menu/rosterstart" method="post">
                    <div class="form-group">
                    <input type="hidden" id="pstid" name="pstid" value="<?=$uid?>" class="form-control mt-3">
                    <lable>Select BD Name</lable>
                     <select name="bdid" class="form-control mt-3">
                         <?php $bd = $this->Menu_model->get_userbyaid('45');
                         foreach($bd as $bd){?>
                         <option value="<?=$bd->user_id?>"><?=$bd->name?></option>
                         <?php } ?>
                     </select>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Start</button>
                    </div>
                  </form>
                  <?php } else{
                  
                     $roc = $this->Menu_model->get_rostcount($bdid,$uid);
                     $inc = $this->Menu_model->get_initcount($bdid,$uid);
                     $rcont=$roc[0]->cont;
                     $icont=$inc[0]->cont;
                     if($rcont!=$icont){
                  
                  ?>
                    <input type="hidden" id="pstiid" value="<?=$uid?>">
                    <button type="button" id="rreview<?=$bdid?>" value="<?=$bdid?>">Review</button>
                      
                   <?php } else{?>   
                      
                    <form action="<?=base_url();?>Menu/rosterclose" method="post">  
                    <div class="form-group">
                        <input type="hidden" name="pid" value="<?=$uid?>">
                        <input type="hidden" name="bid" value="<?=$bdid?>">
                        <textarea name="closeremark" class="form-control" placeholder="Closeing Remark"></textarea>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Close</button>
                    </div>
                  </form>
                  <?php }}?>
                  
            </div>
          </div>
      </div>   
     </div>     
    </section>
    
 <div id="setreview" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Roster Calling Review</div>
                        <div class="col-lg-12 card-body">
                          <form action="<?=base_url();?>Menu/rcremark" method="post">
                           <input type="hidden" name="pst" value="<?=$uid?>">
                           <input type="hidden" name="bd" value="<?=$bdid?>">
                           <input type="hidden" id="cid" name="cid">
                           <input type="hidden" id="inid" name="inid">
                           <input type="hidden" id="lremark" name="lremark">
                           <a id="cnlink" href="" target="_blank"><lable id="compname"></lable></a><br>
                           <lable><b>Last Review Remark</b></lable><br>
                           <lable id="remark"></lable>
                           <textarea name="cremark" class="form-control mt-3" placeholder="Remark..."></textarea>
                           <div class="form-group text-center">
                                <button type="submit" class="btn btn-success mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                            </div>
                          </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div> 



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">

$('[id^="rreview"]').on('click', function() {
    var bdid = this.value;
    var pstid = document.getElementById("pstiid").value;
    $('#setreview').modal('show');
    $.ajax({
         url:'<?=base_url();?>Menu/rpstc',
         method: 'post',
         data: {bdid: bdid,pstid: pstid},
         dataType: 'json',
         success: function(response){
           var len = response.length;
           $('#compname').text('');
           if(len > 0){
             var compname = response[0].compname;
             var cid = response[0].cid;
             var inid = response[0].inid;
             var remark = response[0].remark;
             document.getElementById("compname").innerHTML = compname;
             document.getElementById("cnlink").href = "CompanyDetails/"+cid;
             document.getElementById("remark").innerHTML = remark;
             document.getElementById("cid").value = cid;
             document.getElementById("inid").value = inid;
             document.getElementById("lremark").value = remark;
           }
         }
       });
});

</script>

          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- jquery-validation -->
<script src="<?=base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?=base_url();?>assets/js/additional-methods.min.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
$(function() {
  $.validator.setDefaults({
    submitHandler: function () {
      alert( "Form successful submitted!" );
    }
  });
});
</script>
</body>
</html>
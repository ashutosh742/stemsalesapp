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
            
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    
<form class="p-3" method="POST" action="<?=base_url();?>/Menu/GoalSetting">
    <select name="quarter">
        <option value="1">April to June</option>
        <option value="2">July to September</option>
        <option value="3">October to December</option>
        <option value="4">January to March</option>
    </select>
    <select name="bdid">
        <?php $bd = $this->Menu_model->get_userbyaid($uid);
        foreach($bd as $bd){
        ?>
        <option value="<?=$bd->user_id?>"><?=$bd->name?></option>
        <?php } ?>
        <?php $bd = $this->Menu_model->get_userbyaid($uid);
        foreach($bd as $b){if($b->type_id==9){
        ?>
        <option value="<?=$b->user_id?>"><?=$b->name?></option>
        <?php }} ?>
    </select>
    <button type="submit" class="bg-primary text-light">Filter</button>
</form>
    
    <!-- /.content-header -->
    <section class="content">
      <div class="container-fluid">
          <div class="card card-danger card-outline col-lg-12 col-md-12 col-sm">
              <div class="card-header"><center><b>Goal Setting</b></center></div>
            <div class="row p-3">
         
             
             <?php
             if(isset($_POST["bdid"])){
                 $bdid= $_POST["bdid"];
                 $quarter = $_POST["quarter"];
                 $mbdc=$this->Menu_model->get_mbdc($bdid);
                 $allwork=$this->Menu_model->get_allwork($bdid,$quarter);
                 $dtravel=$this->Menu_model->get_distincttravel($bdid,$quarter);
                 $bdname = $this->Menu_model->get_userbyid($bdid);
                 $bdname = $bdname[0]->name;
             ?>
             
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="small-box bg-light text-secondary">
              <div class="inner">
                  <?php
                  foreach($mbdc as $mc){?>
                        <p><a href="bdcompanies/0/<?=$bdid?>">Total Companies - <b><?=$mc->a?></b></p></a><hr>
                        <p><a href="bdcompanies/1/<?=$bdid?>">Open - <b><?=$mc->b?></b></a></p><hr>
                        <p><a href="bdcompanies/8/<?=$bdid?>">Open [RPEM] - <b><?=$mc->i?></b></p></a><hr>
                        <p><a href="bdcompanies/2/<?=$bdid?>">Reachout - <b><?=$mc->c?></b></a><hr>
                        <p><a href="bdcompanies/3/<?=$bdid?>">Tentative - <b><?=$mc->d?></b></p></a><hr>
                        <p><a href="bdcompanies/4/<?=$bdid?>">Will-Do-Later - <b><?=$mc->e?></b></p></a><hr>
                        <p><a href="bdcompanies/5/<?=$bdid?>">Not-Interest - <b><?=$mc->f?></b></p></a><hr>
                        <p><a href="bdcompanies/10/<?=$bdid?>">TTD-Reachout - <b><?=$mc->k?></b></p></a><hr>
                        <p><a href="bdcompanies/11/<?=$bdid?>">WNO-Reachout - <b><?=$mc->l?></b></p></a><hr>
                        <p><a href="bdcompanies/6/<?=$bdid?>">Positive - <b><?=$mc->g?></b></p></a><hr>
                        <p><a href="bdcompanies/9/<?=$bdid?>">Very Positive - <b><?=$mc->j?></b></p></a><hr>
                        <p><a href="bdcompanies/12/<?=$bdid?>">Positive NAP - <b><?=$mc->o?></b></p></a><hr>
                        <p><a href="bdcompanies/13/<?=$bdid?>">Very Positive NAP - <b><?=$mc->p?></b></p></a><hr>
                        <p><a href="bdcompanies/7/<?=$bdid?>">Closure - <b><?=$mc->h?></b></p></a><hr>
                        <p><a href="bdcompanies/14/<?=$bdid?>">Focus Funnel - <b><?=$mc->m?></b></p></a><hr>
                        <p><a href="bdcompanies/15/<?=$bdid?>">Upsell Client - <b><?=$mc->n?></b></p></a><hr>
                        <p><a href="bdcompanies/16/<?=$bdid?>">Key Client - <b><?=$mc->q?></b></p></a><hr>
                        <p><a href="bdcompanies/17/<?=$bdid?>">Positive Key Client - <b><?=$mc->r?></b></p></a><hr>
                        <p><a href="bdcompanies/18/<?=$bdid?>">Priority Calling - <b><?=$mc->s?></b></p></a><hr>
                        <?php } ?>
                    </div></div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="small-box bg-light text-secondary">
              <div class="inner">
                        <p><b>Distinct Travel</b><br>
                        <?php foreach($dtravel as $dtr){?>
                        <?=$dtr->city?> (<?=$dtr->dttm?>), 
                        <?php } ?>
                        </p><hr>
                    </div></div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
             <center><h3><?=$bdname?></h3></center>
             <form action="<?=base_url();?>Menu/GoalSetting" method="post">
             <input type="hidden" name="gsuid" value="<?=$uid?>">
             <div class="card-body box-profile row was-validated">
                        <div class="p-3 col-12">
                             <lable><b>Call</b></lable><br>
                             <lable>Total call done in this quarter <?=$allwork[0]->a?></lable>
                             <input type="number" name="tcall" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Email</b></lable><br>
                             <lable>Total Email done in this quarter <?=$allwork[0]->b?></lable>
                             <input type="number" name="email" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Scheduled Meeting</b></lable><br>
                             <lable>Total Scheduled Meeting done in this quarter <?=$allwork[0]->c?></lable>
                             <input type="number" name="meeting" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Bargin Meeting</b></lable><br>
                             <lable>Total Bargin Meeting done in this quarter <?=$allwork[0]->d?></lable>
                             <input type="number" name="meeting" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>MOM</b></lable><br>
                             <lable>Total MOM done in this quarter <?=$allwork[0]->f?></lable>
                             <input type="number" name="meeting" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Praposal</b></lable><br>
                             <lable>Total Praposal done in this quarter <?=$allwork[0]->g?></lable>
                             <input type="number" name="praposal" class="form-control" required="">
                         </div>
                         
                         <div class="p-3 col-12">
                             <lable><b>Total Meeting</b></lable><br>
                             <lable>Total RP Meeting done in this quarter <?=$allwork[0]->l?></lable>
                             <input type="number" name="rpmeeting" class="form-control" required="">
                         </div>
                         
                         <div class="p-3 col-12">
                             <lable><b>Base Location Total Meeting</b></lable><br>
                             <input type="number" name="rpmeeting" class="form-control" required="">
                         </div>
                         
                         <div class="p-3 col-12">
                             <lable><b>Out Station Location Total Meeting</b></lable><br>
                             <input type="number" name="rpmeeting" class="form-control" required="">
                         </div>
                         
                         <div class="p-3 col-12">
                             <lable><b>RP Meeting</b></lable><br>
                             <lable>Total RP Meeting done in this quarter <?=$allwork[0]->l?></lable>
                             <input type="number" name="rpmeeting" class="form-control" required="">
                         </div>
                         
                         <div class="p-3 col-12">
                             <lable><b>Research</b></lable><br>
                             <lable>Total call done in this quarter 0</lable>
                             <input type="number" name="research" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Positive Conversation</b></lable><br>
                             <input type="number" name="positivec" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Very Positive Conversation</b></lable><br>
                             <input type="number" name="vpositivec" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Clouser Conversation</b></lable><br>
                             <input type="number" name="clouserc" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>No of School</b></lable><br>
                             <lable>Total No of School <?=$allwork[0]->l?></lable>
                             <input type="number" name="noofschool" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Total Revenue</b></lable><br>
                             <lable>Total Revenue <?=$allwork[0]->l?></lable>
                             <input type="number" name="noofschool" class="form-control" required="">
                         </div>
                         <div class="p-3 col-12">
                             <lable><b>Remark</b></lable><br>
                             <textarea name="gsremark" class="form-control" required=""></textarea>
                         </div>
                         <div class="col-lg-4 col-sm">
                             <button type="submit" class="btn btn-info mt-4" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                         </div>
                      </div>
                  </form>
            </div>
            </div>
            <!-- small box -->
            
          
                  <?php } ?>
             </div>
         </div>
     </div>
    </section>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>

document.getElementById("GoalSetting").style.display = 'none';
function showgs(){
    var abc = document.getElementById("GoalSetting").style.display;
    
    
    if (abc=='block') {
            document.getElementById("GoalSetting").style.display = 'none';
    } 
    else{
            document.getElementById("GoalSetting").style.display = 'block';
    }
    
        
}



$('#reviewtype').on('change', function b() {
  var reviewtype = document.getElementById("reviewtype").value;
  var currentDate = new Date();
  
  if(reviewtype=='Weekly'){
      currentDate.setDate(currentDate.getDate() - 7);
  }
  if(reviewtype=='Fortnightly'){
      currentDate.setDate(currentDate.getDate() - 15);
  }
  if(reviewtype=='Monthly'){
      currentDate.setDate(currentDate.getDate() - 30);
  }
  if(reviewtype=='Querterly'){
      currentDate.setDate(currentDate.getDate() - 90);
  }

  var formattedDate = currentDate.toISOString().slice(0,10);
  document.getElementById("fixdate").value = formattedDate;
});

function myFunction() {
  var checkBox = document.getElementById("myCheckbox");
  if (checkBox.checked == true){
    document.getElementById("fixdate").readOnly = false;
  } else {
    document.getElementById("fixdate").readOnly = true;
  }
  
}

$('#statusid').on('change', function b() {
var pstid = document.getElementById("pstid").value;
var stid = document.getElementById("statusid").value;
var bdid = document.getElementById("bdid").value;
var fdate = document.getElementById("fdate").value;
$.ajax({
url:'<?=base_url();?>Menu/getbdcmpdbynst',
type: "POST",
data: {
pstid: pstid,
stid: stid,
bdid: bdid,
fdate: fdate
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
});



$('#inid').on('change', function b() {
var inid = document.getElementById("inid").value;
var fdate = document.getElementById("fdate").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmpnlog',
type: "POST",
data: {
inid: inid,
fdate: fdate
},
cache: false,
success: function a(result){
$("#cmpdata").html(result);
}
});
});


$('#inid').on('change', function b() {
var inid = document.getElementById("inid").value;
var fdate = document.getElementById("fdate").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmplogs',
type: "POST",
data: {
inid: inid,
fdate: fdate
},
cache: false,
success: function a(result){
$("#cmplogs").html(result);
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
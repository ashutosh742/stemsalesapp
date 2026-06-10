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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <style>


.circle-image img{

	border: 6px solid #fff;
    border-radius: 100%;
    padding: 0px;
    top: -28px;
    position: relative;
    width: 70px;
    height: 70px;
    border-radius: 100%;
    z-index: 1;
    background: #e7d184;
    cursor: pointer;

}


.dot {
      height: 18px;
    width: 18px;
    background-color: blue;
    border-radius: 50%;
    display: inline-block;
    position: relative;
    border: 3px solid #fff;
    top: -48px;
    left: 186px;
    z-index: 1000;
}

.name{
	margin-top: -21px;
	font-size: 18px;
}


.fw-500{
	font-weight: 500 !important;
}


.start{

	color: green;
}

.stop{
	color: red;
}


.rate{

	border-bottom-right-radius: 12px;
	border-bottom-left-radius: 12px;

}



.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center
}

.rating>input {
    display: none
}

.rating>label {
    position: relative;
    width: 1em;
    font-size: 30px;
    font-weight: 300;
    color: #FFD600;
    cursor: pointer
}

.rating>label::before {
    content: "\2605";
    position: absolute;
    opacity: 0
}

.rating>label:hover:before,
.rating>label:hover~label:before {
    opacity: 1 !important
}

.rating>input:checked~label:before {
    opacity: 1
}

.rating:hover>input:checked~label:before {
    opacity: 0.4
}


.buttons{
	top: 36px;
    position: relative;
}


.rating-submit{
	border-radius: 15px;
	color: #fff;
	    height: 49px;
}


.rating-submit:hover{
	
	color: #fff;
}
</style>
  
  
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
    
<section class="content">
      <div class="container-fluid">
       <div class="row p-3">
          <div class="col-sm col-md-4 col-lg-4">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <div class="d-flex flex-column align-items-center text-center">
                    <div class="mt-3">
                       <h4><?=$spd[0]->sname?></h4>
                      <p class="text-secondary mb-1"><?=$spd[0]->saddress?></p>
                      <p class="text-secondary mb-1"><?=$spd[0]->scity?></p>
                      <p class="text-secondary mb-1"><?=$spd[0]->sstate?></p>
                      <hr>
                        
                      <h4><?=$spd[0]->clientname?></h4>
                      <p class="text-secondary mb-1"><?=$pcode=$spd[0]->project_code?></p>
                      
                      <?php  
                         $program= $this->Menu_model->get_programbypc($pcode);
                         $bdid = $program[0]->bd_id;
                         $bddata = $this->Menu_model->get_bdnamebyid($bdid);
                         $ptimeline= $this->Menu_model->get_programtimeline($pcode);
                         $cdate = date('Y-m-d H:i:s');
                         $piid = $spd[0]->pi_id;
                         $piid= $this->Menu_model->get_user_byid($piid);
                         $indid = $spd[0]->ins_id;
                         $indid= $this->Menu_model->get_user_byid($indid);
                      ?>
                      <hr>BD Involve<br><b><?=$bddata[0]->name?></b>
                      <hr>PIA Involve<br><b><?=$piid[0]->fullname?></b>
                      <hr>IMP Involve<br><b><?=$indid[0]->fullname?></b>
                      
                        <hr>
                        <h6 class="d-flex align-items-center mb-3"><i class="material-icons text-info mr-2">This Year Task</i></h6>
                        <?php foreach($programd1 as $pd1){?>
                        <a class="btn btn-primary" style="background-color: #3b5998;" href="#!" role="button"><?=$pd1->cont?> <?=$pd1->action?></a>
                        <?php } ?>
                        <hr>
                        <h6 class="d-flex align-items-center mb-3"><i class="material-icons text-info mr-2">This Year Sub Task</i></h6>
                        <?php foreach($programd2 as $pd2){?>
                        <a class="btn btn-primary" style="background-color: #3b5998;" href="#!" role="button"><?=$pd2->cont?> <?=$pd2->tt?> (<?=$pd2->stt?>)</a>
                        <?php } ?>
                    </div>
                  </div>
              </div>
              
              
              
                
            </div>
          </div>
          <div class="col-sm col-md-8 col-lg-8">
          <div class="card col-sm col-md-12 col-lg-12">
           <div class="row p-3">   
               <?php $i=1; foreach($wgd as $wg){?>
                    <div class="col-sm col-lg-4">
                           <?php $ext = pathinfo($wg->filepath, PATHINFO_EXTENSION); if($ext=='mp4'){?>
                           <a href="https://stemoppapp.in/<?=$wg->filepath?>" target="_blank">
                              <video class="embed-responsive-item img-thumbnail" src="https://stemoppapp.in/<?=$wg->filepath?>" height="300" muted controls></video>
                           </a>
                           <?php }else{?>
                           <a href="https://stemoppapp.in/<?=$wg->filepath?>" target="_blank">
                              <img src="https://stemoppapp.in/<?=$wg->filepath?>"  class="img-thumbnail" width="100%">
                           </a>
                           <?php }?>
                        </div>
                    <?php }?>
                    
                    <div class="col-12 text-center"><hr>Reporting Manager Remark<br><h5><?=$wgd[0]->zmremark?> (<?=$wgd[0]->rating?> Star) <?=$wgd[0]->zmrdt?></h5><hr></div>
                    
                    <?php if($wgd[0]->bdremark==''){?>
                    
                    <form action="<?=base_url();?>Menu/addurwag" method="post" enctype="multipart/form-data">
                      <input type="hidden" name="sid" value="<?=$sid?>">
                      <input type="hidden" name="tid" value="<?=$tid?>">
                        <div class="from-group mt-3">
                            <h5 class="text-center"><input type="hidden" name="que[]" value="">Rating Utilisation</h5>
                            <div class="rating">
                                <input type="radio" name="rat1" value="5" id="5"><label for="5">☆</label>
                                <input type="radio" name="rat1" value="4" id="4"><label for="4">☆</label>
                                <input type="radio" name="rat1" value="3" id="3"><label for="3">☆</label>
                                <input type="radio" name="rat1" value="2" id="2"><label for="2">☆</label>
                                <input type="radio" name="rat1" value="1" id="1"><label for="1">☆</label>
                            </div>
                        </div>
                       <h5>Remark:<?=$wg->remark?></h5>
                       <ul class="list-group list-group-unbordered mb-3">
                       <lable>Select Comment</lable>
                       <select id="sel" class="form-control  p-3  mt-2">
                            <option>Just Photo</option>
                            <option>Not Good</option>
                            <option>Good</option>
                            <option>Good For Social Media</option>
                            <option>Good For Annual Report</option>
                            <option>Good For Social Media and Annual Report</option>
                            <option>Other</option>
                       </select>
                       <textarea id="remark" name="remark" class="form-control  p-3  mt-2" placeholder="Remark" readonly required></textarea>
                       <!-- /.card-body -->
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                    </div>
              </form>
              <?php } else {?>
              <div class="col-12 text-center"><hr>BD Remark<br><h5><?=$wgd[0]->bdremark?> (<?=$wgd[0]->bdrating?> Star) <?=$wgd[0]->bdrdt?></h5><hr></div>
              <?php } ?>
                    
              </div>
            </div>
        </div>



                               
      </div>
                </div></div>                         
                               
                               
  
  
  
  
  
                </fieldset>
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






                
                
         
         
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
$('#sel').on('change', function a() { 
 var sel = this.value;
 if(sel=='Other'){
     document.getElementById("remark").readOnly = false;
 }else{
 document.getElementById("remark").value = sel;}
});


$('input[name=r1]').on('change', function() {
   var data = $('input[name=r1]:checked').val();
   if(data=='YES')
   {
       document.getElementById("remark").style.display = "none";
   }else{
       document.getElementById("remark").style.display = "block";
   }
  
});



$(function () {
  $(".tab").hide();
  $(".tab-input").change(function () {
    if ($(this).val() == "No")
      $("#" + $(this).attr("name")).show();
    else
      $("#" + $(this).attr("name")).hide();
  });
});


$(function () {
    $('.select2').select2()
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
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

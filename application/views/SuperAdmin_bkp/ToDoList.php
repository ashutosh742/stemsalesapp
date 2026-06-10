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
  <link rel="stylesheet" href="<?=base_url();?>assets/css/mystyle.css">
   <!-- DataTables -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
  <style>
      .scrollme {
    overflow-x: auto;
}

table {
  width: 90%;
}

  </style>
  

  
</head>


<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
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



    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <button type="button" id="Daily_Target" class="btn btn-info m-3">Assign Daily Target</button>
          <button type="button" id="Weekly_Objectives" class="btn btn-info m-3">Assign Weekly Objectives</button>
        <div class="card">
            <div class="row">
            <div class="col-lg-12 col-sm">
                <div class="bg-success text-center p-3"><h2>Annual goals</h2></div>
            </div>
            <div class="col-lg-4 col-sm p-3">
                <div class="card">
                  <div class="card-header bg-success text-center"><h5>Weekly Objectives</h5></div>
                  <div class="card-body">
                  </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm p-3">
                <div class="card">
                  <div class="card-header bg-success text-center"><h5>To Do Items</h5></div>
                  <div class="card-body">
                  </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm p-3">
                <div class="card">
                  <div class="card-header bg-success text-center"><h5>Deadlines This Week</h5></div>
                  <div class="card-body">
                  </div>
                </div>
            </div>
            <?php $sdate = date('Y-m-d',strtotime('Previous Monday')); ?>
            <div class="col-12 col-sm p-3">
                  <div class="row p-1">
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br> Monday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                     
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br> Tuesday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br> Wednesday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br>Thursday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br> Friday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br> Saturday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                      <div class="col p-1">
                            <div class="card">
                                <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                              <div class="card-header bg-success text-center"><?=$fDate?> <br> Sunday</div>
                              <div class="card-body">
                                      <?php foreach($task as $ta){?>
                                      <p><?=$ta->task?></p><hr>
                                      <?php } ?>
                              </div>
                            </div>
                        </div>
                      
                  </div>
                </div>
            </div>
            </div>
            <div class="col-12 col-sm p-3">
                <div class="card">
                  <div class="card-header bg-success text-center"><h5>Notes</h5></div>
                  <div class="card-body">
                  </div>
                </div>
            </div>
            </div>
        </div>
          <!-- ./col -->
              
        </div><!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
</section>


<div id="doaction1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Assign Daily Target</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/DailyTarget')?>
                           <input type="datetime-local" name="date1" class="form-control" min="<?=date('Y-m-d')?>">
                           <Select class="form-control" id="dep" name="dep1">   
                               <option>Factory</option>
                               <option>Operation</option>
                               <option>Sales</option>
                           </Select>
                           <input type="text" list="options1" placeholder="Search User" class="form-control" name="user1">
                           <datalist id="options1">
                              <?php $user = $this->Menu_model->get_active_user1(); foreach($user as $us){?>
                              <option value="<?=$us->user_id?>"><?=$us->name?></option>
                              <?php } ?>
                              <?php $user = $this->Menu_model->get_active_user2(); foreach($user as $us){?>
                              <option value="<?=$us->id?>"><?=$us->fullname?></option>
                              <?php } ?>
                              <?php $user = $this->Menu_model->get_active_user3(); foreach($user as $us){?>
                              <option value="<?=$us->id?>"><?=$us->fullname?></option>
                              <?php } ?>
                           </datalist>
                           <input type="text" class="form-control" name="task1" placeholder="Task Name (Heading)"></textarea>
                           <textarea class="form-control" name="remark1" placeholder="Task Detail..."></textarea>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>


<div id="doaction2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                       <div class="card-header bg-info">Assign Weekly Objectives</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/WeeklyObjectives')?>
                           <input type="datetime-local" name="date2" class="form-control" min="<?=date('Y-m-d')?>">
                           <Select class="form-control" id="dep" name="dep2">
                               <option>Factory</option>
                               <option>Operation</option>
                               <option>Sales</option>
                           </Select>
                           <input type="text" list="options2" placeholder="Search User" class="form-control" name="user2">
                           <datalist id="options2">
                              <?php $user = $this->Menu_model->get_active_user1(); foreach($user as $us){?>
                              <option value="<?=$us->user_id?>"><?=$us->name?></option>
                              <?php } ?>
                              <?php $user = $this->Menu_model->get_active_user2(); foreach($user as $us){?>
                              <option value="<?=$us->id?>"><?=$us->fullname?></option>
                              <?php } ?>
                              <?php $user = $this->Menu_model->get_active_user3(); foreach($user as $us){?>
                              <option value="<?=$us->id?>"><?=$us->fullname?></option>
                              <?php } ?>
                           </datalist>
                           <input type="text" class="form-control" name="task2" placeholder="Task Name (Heading)"></textarea>
                           <textarea class="form-control" name="remark2" placeholder="Task Detail..."></textarea>
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

$('#dep').on('change', function f() {
var dep = this.value;
    $.ajax({
    url:'<?=base_url();?>Menu/purpose',
    type: "POST",
    data: {
    action_id: action_id
    },
    cache: false,
    success: function a(result){
    $("#nextpurpose").html(result);
    }
    });
});



$('#Daily_Target').on('click', function b() {
    $('#doaction1').modal('show');
});

$('#Weekly_Objectives').on('click', function b() {
    $('#doaction2').modal('show');
});

</script>
    
   
    
    <!-- /.content -->
  </div></div>
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
      "responsive": false, "lengthChange": false, "autoWidth": false
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    
    $("#example2").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>


</body>
</html>

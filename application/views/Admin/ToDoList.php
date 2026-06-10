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
          <button type="button" id="add_Rremark" class="btn btn-info m-3">Add New Task</button>
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
            <div class="col-12 col-sm p-3">
                <div class="card">
                  <div class="row p-3">
                     <?php $sdate = date('Y-m-d',strtotime('last Monday')); ?>
                      
                      
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;
                                    ?>
                                    
                              <b><?=$fDate?> | Monday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;?>
                              <b><?=$fDate?> | Tuesday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;?>
                              <b><?=$fDate?> | Wednesday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;?>
                              <b><?=$fDate?> | Thursday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;?>
                              <b><?=$fDate?> | Friday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    $sdate=$nfDate;?>
                              <b><?=$fDate?> | Saturday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
                          </div>
                      </div>
                      <div class="col border border-success">
                          <div class="bg-success text-center">
                              <?php $date = DateTime::createFromFormat('Y-m-d', $sdate);
                                    $fDate = $date->format('d F');
                                    $nate = new DateTime($sdate);
                                    $nate->modify('+1 day');
                                    $nfDate = $nate->format('Y-m-d');
                                    $task = $this->Menu_model->get_todo($sdate);
                                    print_r($task);
                                    $sdate=$nfDate;?>
                              <b><?=$fDate?> | Sunday</b>
                          </div>
                          <div>
                              <ul>
                                  <?php foreach($task as $ta){?>
                                  <li><?=$ta->task?></li>
                                  <?php } ?>
                              </ul>
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
                       <div class="card-header bg-info">Create List</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/addtodo')?>
                           <input type="datetime-local" name="date" class="form-control">
                           <select name="dep" class="form-control">
                              <option>Department</option>
                           </select>
                           <textarea class="form-control" name="task" placeholder="Task Detail..."></textarea>
                           
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

$('#add_Rremark').on('click', function b() {
    $('#doaction').modal('show');
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

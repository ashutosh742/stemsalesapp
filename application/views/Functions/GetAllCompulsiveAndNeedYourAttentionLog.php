<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>All Compulsive AndNeed YourAttention Log | STEM APP | WebAPP</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

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
  <?php $this->load->view($dep_name.'/nav.php');?>
      <style>
        .card{
          background: linear-gradient(to right, #ede7f6, #ffffff); 
          border-radius: 12px; 
          box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
          padding: 20px !important;
        }

        th.text-capitalize:nth-child(1),
td:nth-child(1),
th.text-capitalize:nth-child(2),
td:nth-child(2) {
    position: sticky;
    left: 0;
    color: white;
    z-index: 10;
}

tbody tr td:nth-child(1),
tbody tr td:nth-child(2) {
    background-color: white;
    color: black;
}
      </style>

  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
 
<br>
    <section class="content">
      <div class="container-fluid">

        <?php // dd($alllogData); ?>

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                <h3 class="card-title">Compulsive & Need Your Attention Log</h3>
              </div>
              <div class="card-body">
                <div class="text-left">
                <?php 
            $ctype_id = $user['type_id'];
           
            ?>
 <form class="p-3" method="POST" action="<?=base_url();?>/Menu/GetAllCompulsiveAndNeedYourAttentionLog">
  <div class="row">
    <div class="col-md-4" style="align-items: center; justify-content: center; display: flex ;">
    <select name="bdid" class="form-control">
       <option value="">Select User</option>

       <?php foreach ($uniqueUsersLists as $keyid => $userinfo) {
           $userinfotype = $userinfo->type_id;
           ?>
           <option value="<?= $keyid ?>">
               <?= $userinfo ?> 
           </option>
       <?php } ?>
   </select>
    </div>
    <div class="col-md-2" style="align-items: left; justify-content: left; display: flex ;">
    <button type="submit" class="btn btn-primary text-light">Filter</button>
    </div>
    <div class="col-md-6"></div>
  </div>
</form>


                </div>


                <div class="container-fluid">
                            <hr>
         <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>📋 Compulsive & Need Your Attention Log By User</i> <br>
                      <small>
                       Compulsive & Need Your Attention Log By User 📋✨ helps track urgent or repeated actions recorded by team members. 🔄 It highlights patterns that demand focus 🧐 and ensures no critical detail is overlooked. 🚀 Designed to improve accountability, communication 🤝, and quick decision-making ⚡, this log provides clarity for better task management ✅ and timely resolutions ⏳.
                      </small>
                    </h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php foreach ($userRemarksCount as $key => $statuses): ?>

    <?php
      $r = rand(150, 255);
      $g = rand(150, 255);
      $b = rand(150, 255);
      $backgroundColor = "rgba($r, $g, $b,.3)";
  
      $hue = rand(0, 360);
      $saturation = rand(60, 100);
      $lightness = rand(30, 45);
      $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

    // Calculate total
    $total = array_sum($statuses);

     $udetailnamesdatas = $this->Menu_model->get_userbyid($key)[0]->name;
    ?>

    <div class="col-md-3 mb-3">
        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
            <div class="card-body">
                <h5><b><?= $udetailnamesdatas ?></b></h5>
                <h6>Total : <?= $total ?></h6>

                <?php foreach ($statuses as $statusname => $statusValue): ?>
                    <span class="p-2 badge badge-pill badge-dark mt-1" 
                          style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, 
                                 rgba(0, 0, 0, 0.24) 0px 1px 2px;">
                        <strong><?= $statusname ?>:</strong> <?= $statusValue ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php endforeach; ?>


                    </div>
                </div>
                </div>


               
<br>
                  <div class="body-content">
            <div class="page-header">
            <div class="form-group">
           <hr>  
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>📋 Compulsive Task Log</i> <br>
                      <small>
                       📋 Compulsive Task Log helps track repetitive tasks ✍️, ensuring accountability ✅, clarity 🔍, and consistency ⏳. It highlights patterns, improves focus 🎯, and supports personal growth 🚀 through organized daily logging.
                      </small>
                    </h5>

                    </div>
                     <hr>  

                           <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead class='thead-dark'>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>Apply Filter By</th>
                                            <th>Filter Name</th>
                                            <th>Company Name</th>
                                            <th>Log Create</th>
                                            <th>Total Log </th>
                                            <th>Last Task By User Name</th>
                                            <th>Last Task Name</th>
                                            <th>Last Task Update</th>
                                            <th>Last Task Days</th>
                                            <th>Last Task Remarks</th>
                                            <th>Last Review Date</th>
                                            <th>Last Review Type</th>
                                            <th>Cureent Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach($alllogData as $data):
                                            $udetail = $this->Menu_model->get_userbyid($data->user_id);
                                            $apply_log = $udetail[0]->name;

                                            if($data->remarks !== 'Compulsive Task'){
                                              continue;
                                            }
                                      
                                            ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $data->mainbd_name; ?></td>
                                            <td><?= $apply_log; ?></td>
                                            <td><?= $data->remarks; ?></td>
                                            <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$data->cid;  ?>" target="_BLANK"><?= $data->compname; ?></a></td>
                                            <td><?= $data->create_date; ?></td>
                                            <td><?= $data->init_id_count; ?></td>
                                            <td><?= $data->last_task_byuser; ?></td>
                                            <td><?= $data->last_task_name; ?></td>
                                            <td><?= $data->last_task_update; ?></td>
                                            <td><?= $data->last_task_days; ?></td>
                                            <td><?= $data->last_task_remarks; ?></td>
                                            <td><?= $data->last_review_date; ?></td>
                                            <td><?= $data->review_rtype; ?></td>
                                            <td><?php echo $statusName = $this->Menu_model->get_statusbyid($data->cstatus)[0]->name;?></td>
                                        </tr>
                                        <?php $i++; endforeach; ?>
                                  </tbody>
                                </table>
                            </div>

<hr>

   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>📋 Need Your Attention Task Log</i> <br>
                      <small>
                       📋 A focused log to track pending tasks requiring urgent attention ⏳. Stay organized ✅, follow up 🔄, and ensure nothing slips through the cracks 🚀.
                      </small>
                    </h5>

                    </div>

                            <hr>
                           <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                                <table id="example2" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead class='thead-dark'>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>Apply Filter By</th>
                                            <th>Filter Name</th>
                                            <th>Company Name</th>
                                            <th>Log Create</th>
                                            <th>Total Log </th>
                                            <th>Last Task By User Name</th>
                                            <th>Last Task Name</th>
                                            <th>Last Task Update</th>
                                            <th>Last Task Days</th>
                                            <th>Last Task Remarks</th>
                                            <th>Last Review Date</th>
                                            <th>Last Review Type</th>
                                            <th>Cureent Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach($alllogData as $data):
                                            $udetail = $this->Menu_model->get_userbyid($data->user_id);
                                            $apply_log = $udetail[0]->name;
                                      
                                            if($data->remarks !== 'Need Your Attention'){
                                              continue;
                                            }

                                            ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $data->mainbd_name; ?></td>
                                            <td><?= $apply_log; ?></td>
                                            <td><?= $data->remarks; ?></td>
                                            <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$data->cid;  ?>" target="_BLANK"><?= $data->compname; ?></a></td>
                                            <td><?= $data->create_date; ?></td>
                                            <td><?= $data->init_id_count; ?></td>
                                            <td><?= $data->last_task_byuser; ?></td>
                                            <td><?= $data->last_task_name; ?></td>
                                            <td><?= $data->last_task_update; ?></td>
                                            <td><?= $data->last_task_days; ?></td>
                                            <td><?= $data->last_task_remarks; ?></td>
                                            <td><?= $data->last_review_date; ?></td>
                                            <td><?= $data->review_rtype; ?></td>
                                            <td><?php echo $statusName = $this->Menu_model->get_statusbyid($data->cstatus)[0]->name;?></td>
                                        </tr>
                                        <?php $i++; endforeach; ?>
                                  </tbody>
                                </table>
                            </div>


                       
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
      "buttons": ["copy", "csv", "excel"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#example2").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
</script>

</body>
</html>
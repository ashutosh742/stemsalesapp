<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Review Reports | STEM APP</title>
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
      tr, td {
        font-weight: bold;
      }
      .card-header {
        background: floralwhite;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <?php $this->load->view($dep_name.'/nav.php');?>
  <!-- /.navbar -->
<style>
    .card {
    background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
}
</style>
  <hr>
  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h3 class="text-center m-3">BD Wise Review Details</h3>
              </div>

              <div class="card-body">
                <!-- Filter Form -->
                <div class="row mb-4">
                  <div class="col-md-12">
                    <div class="card" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <form action="<?=base_url()?>Reports/ReviewReportBDwise" method="POST" class="form-inline justify-content-center">
                        <div class="form-group mr-3 mb-2">
                          <label for="from_date" class="mr-2">From Date</label>
                          <input type="date" name="sdate" id="from_date" class="form-control" value="<?=isset($sdate) ? $sdate : '';?>">
                        </div>
                        <div class="form-group mr-3 mb-2">
                          <label for="to_date" class="mr-2">To Date</label>
                          <input type="date" name="edate" id="to_date" class="form-control" value="<?=isset($edate) ? $edate : '';?>">
                        </div>
                       <div class="form-group mr-3 mb-2">
                            <label for="review_type" class="mr-2">Review Type</label>
                            <select name="selected_review_types" id="review_type" class="form-control">
                                
                                <option value="all" <?= ($selected_review_types == 'all' ? 'selected' : '') ?>>
                                    All
                                </option>

                                <?php foreach($review_types as $review_type): ?>
                                    <option value="<?= $review_type->id ?>" 
                                        <?= ($selected_review_types == $review_type->id ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($review_type->name) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="form-group mb-2">
                          <button type="submit" class="btn btn-primary">Filter</button>
                          <a href="<?=base_url()?>Reports/ReviewReports" class="btn btn-secondary ml-2">Reset</a>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                  <table id="reviewTable" class="table table-striped table-bordered" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th>#</th>
                        <th>Review ID</th>
                        <th>Review Type</th>
                        <th>Reviewed By</th>
                        <th>On Going</th>
                        <th>Planned Date</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Close Remarks</th>
                        <th>Comments</th>
                        <th>Session Time (hrs)</th>
                        <th>Total Review Users</th>
                        <th>View Report</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      if(!empty($mdata)):
                        $i = 1;
                        foreach($mdata as $review):
                          // Handle close_remarks if it's an array
                          $close_remarks = '';
                          if(isset($review->close_reamrks)) {
                            if(is_array($review->close_reamrks)) {
                              $close_remarks = implode(', ', $review->close_reamrks);
                            } else {
                              $close_remarks = $review->close_reamrks;
                            }
                          }
                      ?>
                      <tr>
                        <td><?=$i++;?></td>
                        <td>
                            <a target="_blank" href="<?= base_url()?>Reports/ReviewReportBDwiseDetails/<?=$review->id;?>">
                                <?=$review->id;?>
                            </a>
                        </td>
                        
                        <td><?=$review->review_types_name;?></td>
                        <td><?=$review->by_name;?></td>
                        <td><?=$review->to_name;?></td>
                        <td><?=date('Y-m-d', strtotime($review->planned_date));?></td>
                        <td><?=date('Y-m-d H:i:s', strtotime($review->start_date));?></td>
                        <td><?=($review->end_date && $review->end_date != '0000-00-00 00:00:00') ? date('Y-m-d H:i:s', strtotime($review->end_date)) : '-';?></td>
                        <td><?php 
                        if($review->review_status == 'Closed'){
                            echo '<span class="badge badge-success">'.$review->review_status.'</span>';
                        }else{
                            echo '<span class="badge badge-danger">'.$review->review_status.'</span>';
                        }
                        ?></td>
                        <td><?=$close_remarks;?></td>
                        <td><?=nl2br($review->comments);?></td>
                        <td>
                            <?php
                                $hours = floor($review->session_time / 60);
                                $minutes = $review->session_time % 60;

                                echo $hours . ' hr ' . $minutes . ' min';
                            ?>
                          </td>
                        <td>
                            <?=$review->total_user;?>
                        </td>
                        <td>
                            <a target="_blank" href="<?= base_url()?>Reports/SingleReviewReportBDwiseDetails/<?=$review->id;?>">
                               view report
                            </a>
                        </td>
                        
                      </tr>
                      <?php endforeach; else: ?>
                      <tr>
                        <td colspan="13" class="text-center">No data found</td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
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
</div>
<!-- ./wrapper -->

<!-- Scripts -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
<script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
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
<script src="<?=base_url();?>assets/js/adminlte.js"></script>
<script src="<?=base_url();?>assets/js/dashboard.js"></script>
<script>
  $(document).ready(function() {
    $('#reviewTable').DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "buttons": ["csv", "excel", "pdf", "print"],
      "pageLength": 25
    }).buttons().container().appendTo('#reviewTable_wrapper .col-md-6:eq(0)');
  });
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Mandatory Restriction for Planner Page | STEM APP | WebAPP</title>
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
      .card-body {
      background: azure;
      }
      .formSection{
      align-items: center;
      justify-content: center;
      display: flex;
      }
      .form-control {
      width: 200px !important;
      }
      .modal-content {
      background: antiquewhite;
      }
      .coladjust {
      padding-right: 5px;
      padding-left: 5px;
      height: 143px;
      align-items: center;
      justify-content: center;
      color: #000;
      }
      .col.card.coladjust1 {
      color: black;
      }
      .col.card.coladjust1 select {
      width: 100% !important;
      }th.sorting {
      background: darkcyan;
      color: white;
      }
      .modal-header {
      background: cadetblue;
      color: white;
      }
      .form-control.is-invalid, .was-validated .form-control:invalid {background-image: none !important;}
      .form-control.is-valid, .was-validated .form-control:valid {background-image: none !important;} 
      .col.card.coladjust1 select {
      width: 100% !important;
      height: 100% !important;
      background: honeydew;
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15), inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
      transition: box-shadow 0.2s ease-in-out;
      }
      .each {
      background: #f1f3f6;
      font-family: Arial, Helvetica, sans-serif;
      border: 5px solid #eaeef5;
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15),
      inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
      transition: box-shadow 0.2s ease-in-out;
      }
      .col.card.coladjust1 {
      text-align: center;
      align-items: center;
      }
      .col.card.coladjust1.p-2.m-1 {
      background: beige;
      }
      input.form-control, .form-row>.col, .form-row>[class*=col-], .container-fluid.body-content.mt-4, table.table-bordered.dataTable, .table-striped tbody tr:nth-of-type(odd) {
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15), inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
      transition: box-shadow 0.2s ease-in-out;
      }
      .card-header  {
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, 0.15), inset -4px -4px 10px white, 0px 0px 2px rgba(255, 255, 255, 0.2);
      transition: box-shadow 0.2s ease-in-out;
      }
      .container-fluid.body-content.mt-4 {
      padding: 20px;
      }
      .col.card.coladjust.m-1 {
      background: cornsilk;
      }
      tbody {
      font-weight: 500;
      }
      .active {
            color: green;
            font-weight: bold;
        }
        .expired {
            color: red;
            font-weight: bold;
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
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <?php if($this->session->flashdata('success_message')): ?>
          <div class="alert alert-success">
            <?php echo $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: #002a1f;">
                    <h2 class="text-center m-3 text-white">Mandatory Restriction for Planner Page</h2>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="each p-5 bg-info1">
                      <div class="was-validated">
                        <form action="<?=base_url();?>Menu/StoreMandatoryRestriction" method="post" id="mandatory_form">
                          <div class="form-row" style="min-height:300px;">
                            <?php $curtype_id = $user['type_id']; ?>
                            <div class="col card coladjust1 p-2 m-1">
                              <lable>Select Target User Type : </lable>
                              <select name="user_type[]" class="form-control" id="user_type" multiple required >
                                <option disabled >Select User Type </option>
                                <?php $get_user_type = $this->Menu_model->get_user_type();
                                  if($curtype_id == 2){
                                      foreach($get_user_type as $at){
                                          if($at->id != 1 && $at->id != 2){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 15){
                                      foreach($get_user_type as $at){
                                          if($at->id == 3 || $at->id == 4 || $at->id == 13){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 4){
                                      foreach($get_user_type as $at){
                                          if($at->id == 3 || $at->id == 13){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 13){
                                      foreach($get_user_type as $at){
                                          if($at->id == 3){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }
                                  ?>
                              </select>
                              <small id="noofuser_type"></small>
                            </div>
                            <div class="col card coladjust1 p-2 m-1">
                              <lable>Select Target User : </lable>
                              <select name="user_id[]" class="form-control" id="user_data" multiple>
                              </select>
                              <small id="noofuser"></small>
                            </div>
                            <div class="col card coladjust44 p-5 m-1" style="background: ghostwhite;">
                                <div class="form-check text-center">
                                    <label>Apply Filter </label>
                                </div>
                                <hr>
                              <div class="form-check" id="company_category_filter">
                                <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="an_pn_in_week" value="yes" >
                                <span data-toggle="tooltip" data-placement="left">AN PN Last Week</span>
                                </label>
                              </div>
                              <div class="form-check" id="company_category_filter">
                                <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="will_give_business" value="yes" >
                                <span data-toggle="tooltip" data-placement="left">Will Give Business</span>
                                </label>
                              </div>
                              <div class="form-check" id="company_category_filter">
                                <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="no_status_changes" value="yes" >
                                <span data-toggle="tooltip" data-placement="left">No Status Changes</span>
                                </label>
                              </div>
                              <div class="form-check" id="company_category_filter">
                                <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="review_target_achived" value="yes" >
                                <span data-toggle="tooltip" data-placement="left">After Review target status achived or not</span>
                                </label>
                              </div>
                              <div class="form-check" id="meetings_done_filter">
                                <label class="form-check-label custom-radio-label">
                                <input type="radio" class="form-check-input" name="meetings_done" value="yes" >
                                <span data-toggle="tooltip" data-placement="left">Meeting Done</span>
                                </label>
                              </div>
                              <hr>
                              <div class="col-6" style="flex: none; max-width: none;">
                                <div class="form-group newcentercls" style="margin-bottom: 0;">
                                  <label>&nbsp;&nbsp;PST Assign : &nbsp;&nbsp;&nbsp;</label>
                                  <div class="form-check-inline">
                                    <label class="form-check-label">
                                    <input type="radio" class="form-check-input" value="yes" name="pst_assign">Yes
                                    </label>
                                  </div>
                                  <div class="form-check-inline">
                                    <label class="form-check-label">
                                    <input type="radio" class="form-check-input" value="no" name="pst_assign">No
                                    </label>
                                  </div>
                                </div>
                              </div>
                              <hr>
                              <div class="col-6" style="flex: none; max-width: none;" >
                                <div class="form-group newcentercls" style="margin-bottom: 0;">
                                  <label>&nbsp;&nbsp;Cluster Assign : </label>
                                  <div class="form-check-inline">
                                    <label class="form-check-label">
                                    <input type="radio" class="form-check-input" value="yes" name="cluster_assign">Yes
                                    </label>
                                  </div>
                                  <div class="form-check-inline">
                                    <label class="form-check-label">
                                    <input type="radio" class="form-check-input" value="no" name="cluster_assign">No
                                    </label>
                                  </div>
                                </div>
                              </div>
                              <hr>
                              <div class="form-check" style="display: inline-flex;padding-left: 0;">
                                <?php $categorys = $this->Menu_model->GetCategories(); ?>
                                <select class="form-control" name="categorys" aria-label=".form-select-sm example" style="padding: 5px; margin: 3px;">
                                        <option>Select Category</option>
                                    <?php foreach($categorys as $cate): ?>
                                        <option value="<?= $cate->dataname; ?>"><?= $cate->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php $get_statuss = $this->Menu_model->get_status(); ?>
                                <select class="form-control" name="status_id" aria-label=".form-select-sm example" style="padding: 5px; margin: 3px;">
                                        <option>Select Status</option>
                                        <?php foreach($get_statuss as $status): ?>
                                            <option value="<?= $status->id; ?>"><?= $status->name; ?></option>
                                        <?php endforeach; ?>
                                </select>
                              </div>
                              <hr>
                              <div class="form-check" style="display: inline-flex;padding-left: 0;">
                              <?php $get_actions = $this->Menu_model->get_action(); ?>
                                    <select class="form-control" name="actions_id" id="actions_id" style="padding: 5px; margin: 3px;" required>
                                    <option>Select Task</option>
                                    <?php foreach($get_actions as $a): 
                                        //if($a->id!=6 && $a->id!=8 && $a->id!=9 && $a->id!=11 && $a->id!=15 && $a->id!=18 && $a->id!=19 && $a->id!=20 && $a->id!=21 && $a->id!=22){
                                        if($a->id==1 || $a->id ==2 || $a->id ==5 ){
                                        ?>
                                        <option value="<?= $a->id; ?>"><?= $a->name; ?></option>
                                    <?php } endforeach; ?>
                                    </select>
                                    <select class="form-control" id="weeksid" name="weeks" style="padding: 5px; margin: 3px;" required>
                                        <option>Select Week</option>
                                        <?php for($i = 1; $i <= 20; $i++){ ?>
                                        <option value="<?= $i; ?>"><?= $i; ?> Week</option>
                                        <?php } ?>
                                    </select>  
                              </div>
                            </div>
                          </div>
                      </div>
                      <br>
                      <hr>
                      <div class="form-row">
                      <div class="col card coladjust m-1">
                      <div>
                      <lable>Restriction Start Date : </lable>
                      <input type="date" class="form-control" min="<?= date('Y-m-d') ?>" id="sdate" name="sdate" placeholder="Start Date" required >
                      </div>
                      </div>
                      <div class="col card m-1 coladjust">
                      <div>
                      <lable>Restriction End Date : </lable>
                      <input type="date" class="form-control" min="<?= date('Y-m-d') ?>" id="edate"  name="edate" placeholder="End Date" required >
                      </div>
                      </div>
                      </div>
                      
                      <hr>
                      <div class="form-row">
                      <div class="col-sm-12 p-3">
                      <center>
                        <button type="submit" id="apply_mandatory_restriction" class="btn mb-2" style="background: #002a1f;color:white;" >Apply Mandatory Restriction </button>
                      </center>
                      </div>
                      </div>
                      </form>
                    </div>
                  </div>
                  <div class="container-fluid body-content mt-4">
                    <div class="page-header">
                    <div class="card-header" style="background: #462103;">
                    <h4 class="text-center m-3 text-white">Mandatory Restriction Applied to User for Planner Page</h4>
                  </div>
                  <br>
                      <div class="table-responsive">
                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th>S.No</th>
                              <th>Target User Name</th>
                              <th>AN PN Last Week</th>
                              <th>Will Give Business</th>
                              <th>No Status Changes</th>
                              <th>After Review target status achived or not</th>
                              <th>Meetings Done</th>
                              <th>PST Assign</th>
                              <th>Cluster Assign</th>
                              <th>Category</th>
                              <th>Company Status</th>
                              <th>Task Actions</th>
                              <th>Week</th>
                              <th>Start Date</th>
                              <th>End Date</th>
                              <th>Created Date</th>
                              <th>Target By</th>
                              <th>Restriction Status</th>
                              <!-- <th>View</th> -->
                            </tr>
                          </thead>
                          <tbody>
                            <?php $i = 1; foreach($targetData as $data): ?>
                            <tr>
                              <td><?= $i; ?></td>
                              <td><?= $data->to_user; ?></td>
                              <td><?= $data->an_pn_in_week; ?></td>
                              <td><?= $data->will_give_business; ?></td>
                              <td><?= $data->no_status_changes; ?></td>
                              <td><?= $data->review_target_achived; ?></td>
                              <td><?= $data->meetings_done; ?></td>
                              <td><?= $data->pst_assign; ?></td>
                              <td><?= $data->cluster_assign; ?></td>
                              <td><?php
                              if($data->category !== ''){
                                $categoryData       = $this->Menu_model->get_categorybyDatabasename($data->category); 
                                $categoryDataName   = $categoryData[0]->name;
                                echo $categoryDataName;
                              }                              
                              ?></td>
                              <td><?php 
                              if($data->status_id !== ''){
                                $statusData     = $this->Menu_model->get_statusbyid($data->status_id); 
                                $statusDataname = $statusData[0]->name;
                                echo $statusDataname;
                              }
                               ?></td>
                              <td><?php 
                              if($data->actions_id != 0){
                                $actionData     = $this->Menu_model->get_actionbyid($data->actions_id); 
                                $actionDataName = $actionData[0]->name;
                                echo $actionDataName;
                              }else{
                                echo "No Action";
                              }
                               ?></td>
                              <td><?= $data->week; ?> Week</td>
                              <td><?= $data->sdate; ?></td>
                              <td><?= $data->edate; ?></td>
                              <td><?= $data->created_at; ?></td>
                              <td><?= $data->by_user; ?></td>
                              <td><?php 
                              $current_date = date('Y-m-d');
                              $status = ($current_date <= $data->edate) ? 'Active' : 'Expired';
                              $status_class = ($status === 'Active') ? 'bg-success p-2' : 'bg-danger p-2';
                              echo '<span class="'.$status_class.'">'.$status.'</span>';
                              ?></td>
                              <!-- <td> <a href="Menu/target_vs_achievement/<?= $data->id; ?>">View</a> </td> -->
                            </tr>
                            <?php $i++; endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
    </div>
    </div>
    </section>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
      "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
    <script type='text/javascript'>  
      function RestrictionUpdate(id,sdate,edate){
      $('#exampleModalCenterdatamom').modal('show');
      $('#res_id').val(id);
      $('#start_date').val(sdate);
      $('#end_date').val(edate);
      }
      
      $('#user_type').on('change', function() {
      
      var user_type_id = $(this).val();
      $.ajax({
      url: '<?=base_url();?>Management/getAllActiveUserInDepartment',
      type: "POST",
      data: {
          user_type_id: user_type_id
      },
      cache: false,
      success: function a(result) {
      $("#user_data").html(result);
      
      var optionCount = $('#user_data').find('option').length;
      $("#noofuser").text("Total Number of User: " + optionCount);
      $("#noofuser").css({"color": "green"});
      }
      });
      });


      $(document).ready(function() {
            $('#apply_mandatory_restriction').on('click', function(event) {
                event.preventDefault(); 
                var actions_id  = $("#actions_id").val();
                var sdate       = $("#sdate").val();
                var edate       = $("#edate").val();
                var weeksid     = $("#weeksid").val();
                if(actions_id == 'Select Task'){
                    alert('* Please select a task to apply restriction.');
                    return false;
                }else if(weeksid == 'Select Week'){
                    alert('* Please select Week to apply restriction.');
                    return false;
                }else if(sdate == ''){
                    alert('* Please select start Date to apply restriction.');
                    return false;
                }else if(edate == ''){
                    alert('* Please select end Date to apply restriction.');
                    return false;
                }else{
                    $('#mandatory_form').submit();  
                    return true;
                } 
            });
        });
    </script>
  </body>
</html>
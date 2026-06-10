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
              <div class="col-sm-6"></div>
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
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                        <div class="text-center mb-2">
                            <div class="bg-primary p-2">
                                <h3>Total <?= $mdata1cnt ?></h3>
                            </div>
                        </div>
                      <?php 
                        // echo "<pre>";
                        // print_r($mdata);
                        ?>
                      <div class="page-header">
                        <div class="table-responsive">
                        <form action="<?=base_url()?>Menu/UpdateAnnualReviewDataFirstTime" method="post">
                        <table id="example11" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead class="bg-dark">
                              <tr>
                                <th>S.No.</th>
                                <th>Company Name (CID)</th>
                                <th>Review By</th>
                                <th>Current Year Focus Funnel </th>
                                <th>Q1 Closure</th>
                                <th>To be Nurture in Q1 Closure</th>
                                <th>keep_company</th>
                                <th>keepremark</th>
                                <th>Review Remarks</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; foreach($mdata as $data){

                                $current_year_focus_funnel  = $data->current_year_focus_funnel;
                                $q1_closure                 = $data->q1_closure;
                                $to_be_Nurture_in_q1        = $data->to_be_Nurture_in_q1;
                                $keep_company               = $data->keep_company;
                                $by_review_name             = $data->by_review_name;


                                if($current_year_focus_funnel == 'yes'){
                                    $focus_funnel_yes_selected = 'selected';
                                }else if($current_year_focus_funnel == 'no'){
                                    $focus_funnel_no_selected = 'selected';
                                }else{
                                    $focus_funnel_yes_selected = '';
                                    $focus_funnel_no_selected = '';
                                }

                                if($q1_closure == 'Yes'){
                                    $q1_closure_yes_selected = 'selected';
                                }else if($q1_closure == 'No'){
                                    $q1_closure_no_selected = 'selected';
                                }else{
                                    $q1_closure_yes_selected = '';
                                    $q1_closure_no_selected = '';
                                }

                                if($to_be_Nurture_in_q1 == 'Yes'){
                                    $to_be_Nurture_in_q1_yes_selected = 'selected';
                                }else if($to_be_Nurture_in_q1 == 'No'){
                                    $to_be_Nurture_in_q1_no_selected = 'selected';
                                }else{
                                    $to_be_Nurture_in_q1_yes_selected = '';
                                    $to_be_Nurture_in_q1_no_selected = '';
                                }
                                
                                if($keep_company == 'yes'){
                                    $keep_company_yes_selected = 'selected';
                                }else if($keep_company == 'no'){
                                    $keep_company_no_selected = 'selected';
                                }else{
                                    $keep_company_yes_selected = '';
                                    $keep_company_no_selected = '';
                                }
                                
                                ?>
                                <tr>
                                    <td><?=$i?></td>
                                    <td>
                                    <input type="hidden" name="annual_review_id[]" value="<?=$data->id?>">
                                    <a href="<?=base_url()?>Menu/CompanyDetails/<?=$data->cid?>">
                                        <?=$data->compname?>( <?=$data->cid?> )
                                        </a>
                                    </td>
                                    <td>
                                        <?= $by_review_name; ?> 
                                    </td>
                                    <td>
                                        <select class="form-control" name="current_year_focus_funnel[]" required>
                                            <option value="">select</option>
                                            <option value="yes" <?=$focus_funnel_yes_selected?>>yes</option>
                                            <option value="no" <?=$focus_funnel_no_selected?>>no</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="q1_closure[]" required>
                                            <option value="">select</option>
                                            <option value="Yes" <?=$q1_closure_yes_selected?>>Yes</option>
                                            <option value="No" <?=$q1_closure_no_selected?>>No</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="to_be_Nurture_in_q1[]" required>
                                            <option value="">select</option>
                                            <option value="Yes" <?=$to_be_Nurture_in_q1_yes_selected?>>Yes</option>
                                            <option value="No" <?=$to_be_Nurture_in_q1_no_selected?>>No</option>
                                        </select>
                                    </td>
                                    <td>
                                    <select class="form-control" name="keep_company[]" readonly="" disabled required>
                                        <option value="">select</option>
                                        <option value="yes" <?= ($keep_company == 'yes') ? 'selected' : '' ?>>yes</option>
                                        <option value="no" <?= ($keep_company == 'no') ? 'selected' : '' ?>>no</option>
                                    </select>
                                    </td>
                                   
                                    <td>
                                        <?= $data->keepremark;?>
                                    </td>
                                    <td>
                                        <?= $data->remarks;?>
                                    </td>
                                    
                                </tr>
                              <?php $i++; }  ?>
                            </tbody>
                          </table>
                          <center>
                            <button type="submit" class="btn btn-primary">Submit</button>
                          </center>
                        </form>
                        </div>
                      </div>
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
      "buttons": ["csv", "excel", "pdf", "print",]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      $(document).ready(function() {

    $("select[name='q1_closure[]'], select[name='to_be_Nurture_in_q1[]'],select[name='keep_company[]']").on("change", function() {
        let row = $(this).closest("tr");

        let q1Closure = row.find("select[name='q1_closure[]']");
        let toBeNurture = row.find("select[name='to_be_Nurture_in_q1[]']");
        let currentFocus = row.find("select[name='current_year_focus_funnel[]']");
        let keep_company = row.find("select[name='keep_company[]']");

        // Check if current_year_focus_funnel is selected as "Yes"
     
            if ($(this).attr("name") === "q1_closure[]") {
                if ($(this).val() === "Yes") {
                    toBeNurture.val("No");
                    currentFocus.val("yes");
                } else if ($(this).val() === "No") {
                    toBeNurture.val("Yes");
                }
            } else if ($(this).attr("name") === "to_be_Nurture_in_q1[]") {
                if ($(this).val() === "Yes") {
                    q1Closure.val("No");
                } else if ($(this).val() === "No") {
                    q1Closure.val("Yes");
                    currentFocus.val("yes");
                }
            }else if ($(this).attr("name") === "keep_company[]") {
                if ($(this).val() === "no") {
                    toBeNurture.val("No");
                    toBeNurture.val("No");
                    currentFocus.val("no");
                }
        }

    });
});

$(document).ready(function() {
    function updateFields(row) {
        let q1Closure = row.find("select[name='q1_closure[]']");
        let toBeNurture = row.find("select[name='to_be_Nurture_in_q1[]']");
        let currentFocus = row.find("select[name='current_year_focus_funnel[]']");
        let keepCompany = row.find("select[name='keep_company[]']");

        if (keepCompany.val() === "no") {
            toBeNurture.val("No");
            currentFocus.val("no");
        }
    }

    // Run on page load for all rows
    $("tr").each(function() {
        updateFields($(this));
    });

    // Run on change event for keep_company select
    $("select[name='keep_company[]']").on("change", function() {
        updateFields($(this).closest("tr"));
    });
});





    </script>
  </body>
</html>
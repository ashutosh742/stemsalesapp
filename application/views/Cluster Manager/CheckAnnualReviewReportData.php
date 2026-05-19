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
      .text-secondary1 {
    color: #ab0101 !important;
}
a.view{
    padding: 4px 10px;
    box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;
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
         <?php 
         // dd($bd_annualData);
         ?>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
               
                <div class="card">
                <div class="text-left p-2">
                <!-- <form class="form-inline">
                <div class="form-group mx-sm-3 mb-2">
                        <label for="financialYearSelect" class="sr-only">Select</label>
                        <select id="financialYearSelect" class="form-control form-control-sm">
                            <?php
                            // $currentYear = date('Y');
                            // $startYear = (date('m') >= 4) ? $currentYear : $currentYear - 1; // Financial year starts in April
                            
                            // for ($i = 0; $i < 1; $i++) {
                            //     $fy = ($startYear - $i) . "-" . ($startYear - $i + 1);
                            //     echo "<option value='$startYear'>$fy</option>";
                            // }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Filter</button>
                    </form> -->
                </div>

                <?php 
                $formatted_string = ucwords(str_replace('_', ' ', $rtype));
                ?>



                  <div class="card-header">
                  <div class="text-center bg-primary p-2">
                            <h3>Annual Review Report ( Company List )</h3>
                        </div>
                        <br>
                    <h3 class="text-center m-3 text-secondary1"><?=$formatted_string;?></h3>
                    <h4 class="text-center m-3 text-secondary1"><?=$tar_name?></h4>
                  </div>
                  <?php $fyear = date("Y", strtotime($financial_Year['start_date'])); ?>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                   
                              <div class="table-responsive">

                              <?php if($rtype == 'total_count' || $rtype == 'pending_review'){ ?>

                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                        <th>S.No</th>
                                        <th>CID</th>
                                        <th>Companies Name</th>
                                        <th>BD Name</th>
                                        <th>Company Current Status</th>
                                        <th>Review Status</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1; ?>

                                        <?php foreach($annualDatasCompanyLists as $companyList){ ?>
                                            <tr>
                                                <td><?=$i?></td>
                                                <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->cid;?></a></td>
                                                <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->compname;?></a></td>
                                                <td><?=$companyList->mainbdname;?></td>
                                                <td><?=$companyList->current_status;?></td>
                                                <td>
                                                    <?php if($companyList->amr_status == 'pending'){?> 
                                                            <span class="bg-warning p-1"><?=$companyList->amr_status;?></td></span>
                                                        <?php }else{?>
                                                            <span class="bg-success p-1"><?=$companyList->amr_status;?></td></span>
                                                            <?php } ?>
                                            </tr>
                                        <?php $i++;} ?>
                                  </tbody>
                                </table>
                                <?php }else{ ?>

                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                        <th>S.No</th>
                                        <th>CID</th>
                                        <th>Companies Name</th>
                                        <th>Main BD Name</th>
                                        <th>Review Done By</th>
                                        <th>Company Current Status</th>
                                        <th>Company Review Time Status</th>
                                        <th>Financial Year</th>
                                        <th>Review Status</th>
                                        <th>BD Marked Current Year Focus Funnel</th>
                                        <th>BD Marked Q1 Closure</th>
                                        <th>BD Marked To Be Nurture in Q1</th>
                                        <th>Keep Company</th>
                                        <th>Keep Remarks</th>
                                        <th>Any Vmeeting</th>
                                        <th>Annaul Focus Positive</th>
                                        <th>Annaul Topspender</th>
                                        <th>Annaul Upsell </th>
                                        <th>Annaul Revenue</th>
                                        <th>Transfer The Funnel</th>
                                        <th>Transfer Locations</th>
                                        <th>Review Date</th>
                                        <th>Review Final Remarks</th>
                                        <th>SC Agree Or Not</th>
                                        <th>Agree Or DisAgree BY</th>
                                        <th>SC Remarks</th>
                                        <th>View Report</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1; ?>

                                        <?php foreach($annualDatasCompanyLists as $companyList){ ?>
                                            <tr>
                                                <td><?=$i?></td>
                                                <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->cid;?></a></td>
                                                <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->compname;?></a></td>
                                                <td><?=$companyList->mainbdname;?></td>
                                                <td><?=$companyList->review_done_by;?></td>
                                                <td><?=$companyList->current_status;?></td>
                                                <td><?=$companyList->review_time_status_name;?></td>
                                                <td><?=$companyList->financial_year;?></td>
                                                <td>
                                                    <?php if($companyList->amr_status == 'pending'){?> 
                                                            <span class="bg-warning p-1"><?=$companyList->amr_status;?></span>
                                                    <?php }else{?>
                                                        <span class="bg-success p-1"><?=$companyList->amr_status;?></span>
                                                    <?php } ?>
                                                </td>
                                                <td><?=$companyList->current_year_focus_funnel;?></td>
                                                <td><?=$companyList->q1_closure;?></td>
                                                <td><?=$companyList->to_be_Nurture_in_q1;?></td>
                                                <td><?=$companyList->keep_company;?></td>
                                                <td><?=$companyList->keepremark;?></td>
                                                <td><?=$companyList->vmeeting;?></td>
                                                <td><?=$companyList->annaul_focuspositive;?></td>
                                                <td><?=$companyList->annaul_topspender;?></td>
                                                <td><?=$companyList->annaul_upsell;?></td>
                                                <td><?=$companyList->annaul_revenue;?></td>
                                                <td><?=$companyList->transfer_the_funnel;?></td>
                                                <td><?=$companyList->transfer_location;?></td>
                                                <td><?=$companyList->created_at;?></td>
                                                <td><?=$companyList->remarks;?></td>
                                                <td>
                                                <?php if($companyList->sc_agree_or_not == 'Agree'){?> 
                                                            <span class="bg-success p-1"><?=$companyList->sc_agree_or_not;?></span>
                                                    <?php }else if($companyList->sc_agree_or_not == 'Disagree'){?>
                                                        <span class="bg-danger p-1"><?=$companyList->sc_agree_or_not;?></span>
                                                    <?php }else{ ?>
                                                        <span class="bg-warning p-1"><?=$companyList->sc_agree_or_not;?></span>
                                                        <?php } ?>
                                                </td>
                                                <td>
                                                <?php if($companyList->review_apr_by == ''){?> 
                                                    <span class="bg-warning p-1">Pending</span>
                                                    <?php }else{ ?>
                                                        <?=$companyList->review_apr_by_name;?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                <?php if($companyList->review_apr_remarks == ''){?> 
                                                    <span class="bg-warning p-1">Pending</span>
                                                    <?php }else{ ?>
                                                        <?=$companyList->review_apr_remarks;?>
                                                    <?php } ?>
                                                </td>
                                                
                                             
                                              
                                                <td><a class="view" href="<?=base_url().'Menu/CheckAnnualReviewDetailsReport/'.$companyList->amr_id?>">view</a></td>
                                             
                                            </tr>
                                        <?php $i++;} ?>
                                  </tbody>
                                </table>

                                <?php } ?>


                                
                              </div>
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

    </div>    

    <?php
$startYear = date("Y");
$endYear = $startYear + 1;

?>

    <footer class="main-footer">
      <strong>Copyright &copy; <?php echo "$startYear-$endYear"; ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
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
               <form class="form-inline" method="POST" action="<?= htmlspecialchars(base_url().'Menu/NewAnnualReviewReportWithLastYearMeetingsDetails') ?>">

                <div class="form-group mx-sm-3 mb-2">
                        <label for="financialYearSelect" class="sr-only">Select</label>
                        <select name="teaget_fyear" id="financialYearSelect" class="form-control form-control-sm">
                            <option value=''>select</option>
                            <?php
                           foreach($allReviewfinancialYear as $financialYear){
                            if($cfyear == $financialYear->financial_year){
                              $selectted = 'selected';
                            }else{
                              $selectted = '';
                            }
                            echo '<option '.$selectted.' value="'.$financialYear->financial_year .'">'.$financialYear->financial_year .'</option>';
                           }
                            ?>
                        </select>
                        
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                                    <label>Select User</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                   <select class="form-control" name="select_user">
                                    <option value="">Select</option>
                                    <option value="All">All</option>
                                    <option value="BD">BD</option>
                                    <option value="CM">CM</option>
                                    <option value="PST">PST</option>
                                    <option value="EA">EA</option>
                                </select>

                                </div>
                    <button type="submit" class="btn btn-primary mb-2">Filter</button>
                    </form>
                </div>
                  <div class="card-header">
                  <div class="text-center bg-primary p-2">
                            <h3>Annual Review Report With (Meeting Details By User)</h3>
                            <h4 class="text-center m-3"><?= $cfyear; ?></h4>
                        </div>
                        <br>
                
                  </div>
                  <?php $fyear = date("Y", strtotime($financial_Year['start_date'])); ?>
                  <!-- /.card-header -->

                  <?php $commingusertype = $user['type_id']; ?>
                  <?php $fyear = $cfyear; ?>


                  <?php // die; ?>
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                              <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                        <th>S.No</th>
                                        <th>BD Name</th>
                                        <th>Total Companies</th>
                                        <th>Complete Review</th>
                                        <th>Pending Review</th>
                                        <th>BD Marked Current Year Focus Funnel</th>
                                        <th>BD Marked Q1 Closure</th>
                                        <th>BD Marked To Be Nurture in Q1</th>
                                        
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php $i=1; ?>

                                    <?php if($commingusertype  == 2 || $commingusertype  == 3 || $commingusertype  == 4 || $commingusertype  == 13 || $commingusertype  == 15 && ($commingusertype  !== 17)) { ?>

                                    <?php foreach($bd_annualData as $adata){ ?>
                                            <tr>
                                            <td><?=$i?></td>

                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewSessionReportData/'.$fyear.'/'.$adata->user_id.'/check_session'?>"><?=$adata->username;?></a></td>
                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/total_count'?>"><?=$adata->total_count;?></a></td>
                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/complete_review'?>"><?=$adata->complete_review;?></a></td>
                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/pending_review'?>"><?=$adata->pending_review;?></a></td>
                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_current_year_focus_funnel'?>"><?=$adata->bd_marked_current_year_focus_funnel;?></a></td>
                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_q1_closure'?>"><?=$adata->bd_marked_q1_closure;?></a></td>
                                            <td><a href="<?= base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_to_be_Nurture_in_q1'?>"><?=$adata->bd_marked_to_be_Nurture_in_q1;?></a></td>
                                            </tr>
                                        <?php $i++;} ?>

                                        <?php } ?>

                                        <?php if($commingusertype  == 2 || $commingusertype  == 4 || $commingusertype  == 13 || $commingusertype  == 15 && ($commingusertype  !== 17 || $commingusertype  != 3)) { ?>

                                        <?php foreach($cm_annualData as $adata){ ?>
                                          <tr>
                                            <td><?=$i?></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewSessionReportData/'.$fyear.'/'.$adata->user_id.'/check_session'?>"><?=$adata->username;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/total_count'?>"><?=$adata->total_count;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/complete_review'?>"><?=$adata->complete_review;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/pending_review'?>"><?=$adata->pending_review;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_current_year_focus_funnel'?>"><?=$adata->bd_marked_current_year_focus_funnel;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_q1_closure'?>"><?=$adata->bd_marked_q1_closure;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_to_be_Nurture_in_q1'?>"><?=$adata->bd_marked_to_be_Nurture_in_q1;?></a></td>
                                            </tr>
                                        <?php $i++;} ?>

                                        <?php } ?>

                                        <?php if($commingusertype  == 2 || $commingusertype  == 4 || $commingusertype  == 15 && ($commingusertype  != 17 || $commingusertype  != 3 || $commingusertype  != 13)) { ?>
                                  
                                      <?php foreach($pst_annualData as $adata){ ?>
                                          <tr>
                                            <td><?=$i?></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewSessionReportData/'.$fyear.'/'.$adata->user_id.'/check_session'?>"><?=$adata->username;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/total_count'?>"><?=$adata->total_count;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/complete_review'?>"><?=$adata->complete_review;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/pending_review'?>"><?=$adata->pending_review;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_current_year_focus_funnel'?>"><?=$adata->bd_marked_current_year_focus_funnel;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_q1_closure'?>"><?=$adata->bd_marked_q1_closure;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_to_be_Nurture_in_q1'?>"><?=$adata->bd_marked_to_be_Nurture_in_q1;?></a></td>     
                                            </tr>
                                        <?php $i++;} ?> 

                                        <?php } ?>

                                        <?php if($commingusertype  == 2 || $commingusertype  == 17) { ?>
                                       

                                       <?php foreach($ea_annualData as $adata){ ?>
                                          <tr>
                                            <td><?=$i?></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewSessionReportData/'.$fyear.'/'.$adata->user_id.'/check_session'?>"><?=$adata->username;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/total_count'?>"><?=$adata->total_count;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/complete_review'?>"><?=$adata->complete_review;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/pending_review'?>"><?=$adata->pending_review;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_current_year_focus_funnel'?>"><?=$adata->bd_marked_current_year_focus_funnel;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_q1_closure'?>"><?=$adata->bd_marked_q1_closure;?></a></td>
                                            <td><a href="<?=base_url().'Menu/CheckAnnualReviewReportDataWithLastYearMeetings/'.$fyear.'/'.$adata->user_id.'/bd_marked_to_be_Nurture_in_q1'?>"><?=$adata->bd_marked_to_be_Nurture_in_q1;?></a></td>
                                            </tr>
                                        <?php $i++;} ?>
                                        <?php } ?>



                                  </tbody>
                                </table>
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
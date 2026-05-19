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
    td {
    font-weight: 700;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->

      <!-- Navbar -->
      <?php require('nav.php');?>
      <!-- /.navbar -->
       <style>
        /* .content-card {
        background-image: url('https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg');
        background-size: cover;
        background-position: center;
        }
        .glass-card {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
} */

/* .content-card  {
  background-image: url('https://images.unsplash.com/photo-1508144753681-9986d4df99b3?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
  background-size: cover;
  background-position: center;
}



table {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    padding: 20px;
    width: 100%;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
} */
       </style>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
                <section class="content">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-12">
                        <div class="card content-card mt-2">
                          <div class="card-header text-center">
                            <h3 class="text-center m-3" style="color: #f7008d !important;">BD FUNNEL DETAILS</h3>
                            <span class="text-primary"><strong><?=$category;?></strong></span>
                          </div>
                          <br>
                          <form class="form-inline" action="<?=base_url().'Menu/AllBDFunnal/'.$uid;?>" method="POST" >
                                <div class="form-group mx-sm-3 mb-2">
                                    <label>Category</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                   <select class="form-control" name="category">
                                    <option value="">Select</option>
                                    <option value="BD Companies" <?php echo ($category == 'BD Companies') ? 'selected' : ''; ?>>BD Companies</option>
                                    <option value="PST Assign Companies" <?php echo ($category == 'PST Assign Companies') ? 'selected' : ''; ?>>PST Assign Companies</option>
                                    <option value="Total Potential" <?php echo ($category == 'Total Potential') ? 'selected' : ''; ?>>Total Potential</option>
                                    <option value="Total Non Potential" <?php echo ($category == 'Total Non Potential') ? 'selected' : ''; ?>>Total Non Potential</option>
                                    <option value="Total Not Mark Potential/Non Potential" <?php echo ($category == 'Total Not Mark Potential/Non Potential') ? 'selected' : ''; ?>>Total Not Mark Potential/Non Potential</option>
                                    <option value="Potential Mark By PST on BD Company" <?php echo ($category == 'Potential Mark By PST on BD Company') ? 'selected' : ''; ?>>Potential Mark By PST on BD Company</option>
                                    <option value="Non Potential Mark By PST on BD Company" <?php echo ($category == 'Non Potential Mark By PST on BD Company') ? 'selected' : ''; ?>>Non Potential Mark By PST on BD Company</option>
                                    <option value="Not Mark Potential/Non Potential by PST on BD Company" <?php echo ($category == 'Not Mark Potential/Non Potential by PST on BD Company') ? 'selected' : ''; ?>>Not Mark Potential/Non Potential by PST on BD Company</option>
                                    <option value="Key Client" <?php echo ($category == 'Key Client') ? 'selected' : ''; ?>>Key Client</option>
                                    <option value="Positive Key Client" <?php echo ($category == 'Positive Key Client') ? 'selected' : ''; ?>>Positive Key Client</option>
                                    <option value="Top Spender" <?php echo ($category == 'Top Spender') ? 'selected' : ''; ?>>Top Spender</option>
                                    <option value="Upsell Client" <?php echo ($category == 'Upsell Client') ? 'selected' : ''; ?>>Upsell Client</option>
                                    <option value="Focus Funnel" <?php echo ($category == 'Focus Funnel') ? 'selected' : ''; ?>>Focus Funnel</option>
                                </select>

                                </div>
                                <button type="submit" class="btn btn-primary mb-2">Filter</button>
                            </form>
                          <div class="card-body glass-card">
                            <div class="body-content">
                              <div class="page-header">
                                <div class="form-group">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                              <thead class="thead-dark">
                                                <tr>
                                                  <th>S.No</th>
                                                  <th>BD Name</th>
                                                 <?php if($category !==''){ ?> <th>Total BD funnel</th>  <?php } ?>
                                                  <th>Total Companies</th>
                                                  <!-- <th>BD Companies</th>
                                                  <th>PST Assign Companies</th>
                                                  <th>Total Potential</th>
                                                  <th>Total Non Potential</th>
                                                  <th>Total Not Mark Potential/NOn Potential</th>
                                                  <th>Potential Mark By PST on BD Company</th>
                                                  <th>Non Potential Mark By PST on BD Company</th>
                                                  <th>Not Mark Potential/NOn Potential by PST on BD Company</th> -->
                                                  <th>Open</th>
                                                  <th>Open [RPEM]</th>
                                                  <th>Reachout</th>
                                                  <th>Tentative</th>
                                                  <th>Will-Do-Later</th>
                                                  <th>Not-Interest</th>
                                                  <th>TTD-Reachout</th>
                                                  <th>WNO-Reachout</th>
                                                  <th>Positive</th>
                                                  <th>Positive-NAP</th>
                                                  <th>Very Positive</th>
                                                  <th>Very Positive-NAP</th>
                                                  <th>Closure</th>

                                                  <!-- <th>Key Client</th>
                                                  <th>Positive Key Client</th>
                                                  <th>Top Spender</th>
                                                  <th>Upsell Client</th>
                                                  <th>Focus Funnel</th> -->

                                                </tr>
                                              </thead>
                                              <tbody>
                                                <?php
                                                $i=1;
                                                $category = urlencode($category);
                                                foreach($mbdc as $mc){
                                                    $bdid = $mc->current_user_id;
                                                    ?>
                                                <tr>
                                                  <td><?=$i?></td>
                                                  <td><?=$mc->user_name?></td>
                                                  <?php if($category !==''){ ?><td><a href="../AllBDCompanies/0/<?=$bdid?>"><?=$mc->total_bd_funnel?></a></td> <?php } ?>
                                                  <td><a href="../AllBDCompanies/0/<?=$bdid?>?category=<?=$category?>"><?=$mc->a?></a></td>
                                                  
                                                  <!-- <td><a href="../AllBDCompanies/27/<?=$bdid?>"><?=$mc->z?></a></td>
                                                  <td><a href="../AllBDCompanies/33/<?=$bdid?>"><?=$mc->zd?></a></td>
                                                  <td><a href="../AllBDCompanies/21/<?=$bdid?>"><?=$mc->w?></a></td>
                                                  <td><a href="../AllBDCompanies/25/<?=$bdid?>"><?=$mc->x?></a></td>
                                                  <td><a href="../AllBDCompanies/31/<?=$bdid?>"><?=$mc->y?></a></td>
                                                  <td><a href="../AllBDCompanies/28/<?=$bdid?>"><?=$mc->za?></a></td>
                                                  <td><a href="../AllBDCompanies/29/<?=$bdid?>"><?=$mc->zb?></a></td>
                                                  <td><a href="../AllBDCompanies/32/<?=$bdid?>"><?=$mc->zc?></a></td> -->

                                                  <td><a href="../AllBDCompanies/1/<?=$bdid?>?category=<?=$category?>"><?=$mc->b?></a></td>
                                                  <td><a href="../AllBDCompanies/8/<?=$bdid?>?category=<?=$category?>"><?=$mc->i?></a></td>
                                                  <td><a href="../AllBDCompanies/2/<?=$bdid?>?category=<?=$category?>"><?=$mc->c?></a></td>
                                                  <td><a href="../AllBDCompanies/3/<?=$bdid?>?category=<?=$category?>"><?=$mc->d?></a></td>
                                                  <td><a href="../AllBDCompanies/4/<?=$bdid?>?category=<?=$category?>"><?=$mc->e?></a></td>
                                                  <td><a href="../AllBDCompanies/5/<?=$bdid?>?category=<?=$category?>"><?=$mc->f?></a></td>
                                                  <td><a href="../AllBDCompanies/10/<?=$bdid?>?category=<?=$category?>"><?=$mc->k?></a></td>
                                                  <td><a href="../AllBDCompanies/11/<?=$bdid?>?category=<?=$category?>"><?=$mc->l?></a></td>
                                                  <td><a href="../AllBDCompanies/6/<?=$bdid?>?category=<?=$category?>"><?=$mc->g?></a></td>
                                                  <td><a href="../AllBDCompanies/12/<?=$bdid?>?category=<?=$category?>"><?=$mc->o?></a></td>
                                                  <td><a href="../AllBDCompanies/9/<?=$bdid?>?category=<?=$category?>"><?=$mc->j?></a></td>
                                                  <td><a href="../AllBDCompanies/13/<?=$bdid?>?category=<?=$category?>"><?=$mc->p?></a></td>
                                                  <td><a href="../AllBDCompanies/7/<?=$bdid?>?category=<?=$category?>"><?=$mc->h?></a></td>

                                                  <!-- <td><a href="../AllBDCompanies/16/<?=$bdid?>"><?=$mc->q?></a></td>
                                                  <td><a href="../AllBDCompanies/17/<?=$bdid?>"><?=$mc->r?></a></td>
                                                  <td><a href="../AllBDCompanies/20/<?=$bdid?>"><?=$mc->t?></a></td>
                                                  <td><a href="../AllBDCompanies/15/<?=$bdid?>"><?=$mc->u?></a></td>
                                                  <td><a href="../AllBDCompanies/14/<?=$bdid?>"><?=$mc->v?></a></td> -->
                                                </tr>
                                                <?php $i++;} ?>
                                              </tbody>
                                            </table>
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
                  "buttons": ["csv", "excel", "pdf", "print"]
                  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                  </script>
                </body>
              </html>
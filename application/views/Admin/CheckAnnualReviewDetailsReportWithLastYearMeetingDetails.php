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
                  <?php if ($this->session->flashdata('pending_message')): ?>
                        <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('pending_message'); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('success_message')): ?>
                        <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('success_message'); ?>
                    </div>
                    <?php endif; ?>
                    <?php
                    if ($this->session->flashdata('error_message')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <?php endif; ?>
                  </div>
                 

                  <?php // dd($amrAllDatas); ?>

                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                        <div class="text-center bg-primary p-2">
                            <h3>Last Year Meetings Details</h3>
                            <?php 
                            
                            $utype = $this->Menu_model->get_userbyid($tar_uid); 
                            $utypename = $utype[0]->name; 
                            echo $utypename;
                            
                            ?>
                            <br>
                            <?=$fyear;?>
                        </div>
                        <br>
                        <div class="table-responsive">
                          <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead class="thead-dark">
                              <tr>
                                <th>S.No</th>
                                <th>CID</th>
                                <th>Companies Name</th>
                                <th>Company Current Status</th>
                                <th>Company Last Status</th>
                                <th>Main BD Name</th>
                                <th>Task Done By</th>

                                <th>Task Name</th>
                                <th>Task Plan Time Company Status</th>
                                <th>Meetings Date</th>
                                <th>Start Meetings</th>
                                <th>Close Meetings</th>
                                <th>Meetings Status</th>
                                <th>Remarks</th>
                                <th>View Mom</th>

                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; ?>
                              <?php foreach($amrAllDatas as $companyList){ ?>
                              <tr>
                                <td><?=$i?></td>
                                <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->cid;?></a></td>
                                <td><a href="<?=base_url().'Menu/CompanyDetails/'.$companyList->cid?>"><?=$companyList->compname;?></a></td>
                               
                                <td><?=$companyList->current_status;?></td>
                                <td><?=$companyList->last_status;?></td>
                              

                                <td><?=$companyList->main_bd_name;?></td>
                                <td><?=$companyList->task_done_by;?></td>
                                <td><?=$companyList->task_name;?></td>
                                <td><?=$companyList->task_plan_time_status;?></td>
                                <td><?=$companyList->storedt;?></td>
                                <td><?=$companyList->startm;?></td>
                                <td><?=$companyList->closem;?></td>
                                <td><?=$companyList->meeting_status;?></td>
                                <td><?=$companyList->remarks;?></td>

                                <td>
                                    <?php if(!is_null($companyList->mom_id)){ ?>
                                    <a href="<?=base_url();?>/Management/MomDataCheckInAdmin/<?=$companyList->mom_id?>/<?=$companyList->task_id?>">view
                                    <?php }else{  ?>
                                       
                                        <br>
                                        
                                        <?php
                                        if($companyList->remarks == 'Meeting Close With RP'){
                                          $init_call_id = $companyList->init_call_id;
                                          $task_id      = $companyList->task_id;
                                          $getMomData   = $this->Menu_model->GetOldMOMDataWithInitIdAndTaskID($init_id,$task_id);
                                          $nextCFID  = $getMomData[0]->nextCFID;
                                          
                                          if($nextCFID != 0){
                                          $getMomDatacnt = sizeof($getMomData);
                                          if($getMomDatacnt > 0){
                                            $momData = $getMomData[0]->mom;
                                            echo $momData;
                                          }else{
                                            echo "N/A";
                                          }
                                        }else{
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                        }

                                        }else{
                                          echo "N/A";
                                        }
                                        ?>
                                    <?php } ?>
                                </td>
                                
                                
                               
                              </tr>
                              <?php $i++;} ?>
                            </tbody>
                          </table>
                        </div>
                
                        <br>

                        <div class="text-center">
                    
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
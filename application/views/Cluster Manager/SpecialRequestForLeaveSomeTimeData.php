<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Special Request For Plan Change | STEM APP | WebAPP</title>
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
              <div class="col-sm-12 card bg-primary p-2 text-center">
                <h1 class="m-0">All Special Request For Plan Change</h1>
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
          <div class="container-fluid">
            <?php
              if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('reject_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('reject_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title"></h3>
                  </div>
                  <!-- /.card-header -->
                  <?php 
                    //$requests = $this->Menu_model->getSpecialRequestForLeaveData($uid);
                    
                    //dd($requests);
                    
                    ?>
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                            <form method='POST'  enctype="multipart/form-data">
                              <div class="table-responsive">
                                <div class="table-responsive">
                                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                      <tr>
                                        <th>S.No</th>
                                        <th>User Name</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                         <!-- <th>Task Between Time</th> -->
                                        <th>Leave Purpose</th>
                                        <th>Approved By</th>
                                        <th>Approved Status</th>
                                        <th>Approved Date</th>
                                        <th>Approved Remarks</th>
                                        <th>Request Date</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php 
                                        $i =1;

                                        foreach($requests as $req){
                                            
                                           $username =  $this->Menu_model->get_userbyid($req->user_id)[0]->name;
                                           $cdate = date("Y-m-d");
                                           $tasks = $this->Menu_model->getTaskBetweenTimeWithAction($req->user_id,$req->date,$req->stime,$req->etime);
                                           $tasksize = sizeof($tasks);
                                           ?>
                                      <tr>
                                        <td><?=$i ?></td>
                                        <td><?=$username ?></td>
                                        <td><?=$req->date ?></td>
                                        <td><?=$req->stime ?></td>
                                        <td><?=$req->etime ?></td>
                                        <!-- <td> -->
                                          <?php 
                                        // $totoltime = '';
                                        // $totoltask = '';
                                        // echo "<div class='card p-2 mt-1'>";
                                        // foreach($tasks as $item){
                                        //     $act = $this->Menu_model->get_actionbyid($item->aid);
                                        //     $yesttime = $act[0]->yest;
                                        //     $tasktime = $item->cont * $yesttime;
                                        //     $totoltime +=$tasktime;
                                        //     $totoltask +=$item->cont;
                                            
                                        //     echo $item->acname." (".$item->cont.") - ".$tasktime." Minutes<hr/>";
                                        // }
                                        // echo "</div>";
                                        // $hours = floor($totoltime / 60);  // Get the number of full hours
                                        // $remainingMinutes = $totoltime % 60; // Get the remaining minutes
                                        // if($tasksize > 0){
                                        // echo "<div class='card p-2 mt-1'>";
                                        // echo "Total Task : $totoltask <br><hr/>"; 
                                        // echo "Total Time : $hours hours and $remainingMinutes minutes"; 
                                        // echo "</div>";
                                        // }else{
                                        //     echo "<div class='card p-2 mt-1'>";
                                        //     echo "Total Task : 0 <br>"; 
                                        //     echo "</div>";
                                        // }

                                        ?>
                                        <!-- </td> -->
                                        <td><?=$req->prupose ?></td>
                                        <td>
                                          <?php 
                                            if($req->approve_by ==''){
                                                echo "<span class='p-2 bg-warning'>Pending</span>";
                                                }else{
                                                    echo $this->Menu_model->get_userbyid($req->approve_by)[0]->name;
                                                    }
                                                ?>
                                        </td>
                                        <td>
                                          <?php 
                                            if($req->approve_status ==''){
                                                echo "<span class='p-2 bg-warning'>Pending</span>";
                                                }elseif($req->approve_status =='Reject'){
                                                    echo "<span class='p-2 bg-danger'>Reject</span>";
                                                    }elseif($req->approve_status =='Approved'){
                                                        echo "<span class='p-2 bg-success'>Approved</span>";
                                                        }
                                                ?>
                                        </td>
                                        <td>
                                          <?php 
                                            if($req->approve_date ==''){
                                                echo "<span class='p-2 bg-warning'>Pending</span>";
                                                }else{
                                                    echo $req->approve_date;
                                                    }
                                                ?>
                                        </td>
                                        <td>
                                          <?php 
                                            if($req->approve_remarks ==''){
                                                echo "<span class='p-2 bg-warning'>Pending</span>";
                                                }else{
                                                    echo $req->approve_remarks;
                                                    }
                                                ?>
                                        </td>
                                        <td><?=$req->created_at ?></td>
                                        <td>
                                          <?php 
                                        //   echo $req->approve_status;
                                          
                                          if($req->approve_status ==''){ ?>
                                          <div>
                                            <center>
                                              <p>
                                                <button type="button" class="btn btn-success" onclick="RequestApprove(<?=$req->id?>,'Approved')">Approve</button> 
                                              <hr>
                                              <button type="button" class="btn btn-primary" onclick="RequestReject(<?=$req->id?>,'Reject')">Reject</button>
                                              </p>
                                            </center>
                                          </div>
                                          <?php }else{ ?>
                                              <?php echo "Success" ?>
                                          <?php }?>
                                        </td>
                                      </tr>
                                      <?php $i++; } ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </form>
                            <!--END OF FORM ^^-->
                          </fieldset>
                        </div>
                        <hr />
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
    <div class="modal fade" id="exampleModalCenterdataApprove" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle"><span id="ar_title" ></span> Remarks</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <form action="<?=base_url();?>Menu/AdminAcceptSpecialRequest" method="post" >
    <input type="hidden" value="" id="ntid" name="id">
    <input type="hidden" value="" id="status" name="status">                  
    <hr>
    <div class="mb-3 mt-3">
    <textarea id="remarks" name="remarks" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
    </div>
    <div class="form-group text-center">
    <button type="submit" class="btn btn-success mt-2">Submit</button>
    </div>
    </form>
    </div>
    </div>
    </div>
    </div>
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
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
      
      function RequestApprove(id,val){
       
       $('#exampleModalCenterdataApprove').modal('show');
       $('#ntid').val(id);
       $('#ar_title').text('Approved');
       $('#status').val('Approved');
       }
       function RequestReject(id,val){
      
              $('#exampleModalCenterdataApprove').modal('show');
              $('#ntid').val(id);
              $('#ar_title').text('Reject');
              $('#status').val('Reject');
              }
      
    </script>
  </body>
</html>
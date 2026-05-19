<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annual Comany Report | STEM APP| WebAPP</title>
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
    <style>
    .ApprovedStatus{
    padding:6px 10px;
    border-radius:4px;
    }
    .ApprovedStatus.Pending{
    background:orange;
    color:white;
    }
    .ApprovedStatus.Reject{
    background:red;
    color:white;
    }
    .ApprovedStatus.approved{
    background:green;
    color:white;
    }
    .colapsboxsha{
    border-radius:22px;
    box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;
    }
    .modal.fade.show{
    background: rgba(100, 200, 150, 0.4);
    }
    #plandate{
    width:300px;
    }
    .setpaldate{
    display:flex;
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
        <section class="content">
          <div class="container-fluid">
            
            <div class="card p-2 bg-primary">
              <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                  <form class="setpaldate" action="<?=base_url();?>Menu/TodaysTaskApprovelRequest" method="post">
                    <input type="date" class="form-control m-2" name="targetdate" value="<?=$adate?>" required="" id="plandate">
                    <input type="submit" class="btn btn-warning m-2" value="Set Date">
                  </form>
                </div>
              </div>
            </div>
            <div class="row p-3">
              <div class="col-sm col-md-12 col-lg-12 m-auto">
                <?php if ($this->session->flashdata('action_message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong> <?php echo $this->session->flashdata('action_message'); ?></strong>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                
                <?php endif; ?>
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    <div class="bg-warning colapsboxsha text-center mt-2 mb-2">
                      <h3><i>Todays Task Approvel Request</i></h3>
                    </div>
                    <?php
                    $utype = $this->Menu_model->get_userbyid($userid);
                    ?>
                    <div class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapseExample<?=$k?>" role="button" aria-expanded="false" aria-controls="collapseExample<?=$k?>">
                      Request :
                      
                    </div>
                    <div class="collapse show mt-3" id="collapseExample<?=$k?>">
                      <div class="card card-body" style="background: azure;"  >
                        <div class="ApprovedStatus">
                          <!--<h4 class="ApprovedStatus Pending">Changes For Academic year 2024-24</h4> -->
                          <div class="table-responsive">
                            <table id="exampledata" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead>
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">User Name</th>
                                  <th scope="col">Date</th>
                                  <th scope="col">Request Message</th>
                                  <th scope="col">Approvel Status</th>
                                  <th scope="col">Remarks</th>
                                  <th scope="col">Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $j =1;
                                foreach($getreqData as $data){ ?>
                                <tr>
                                  <th><?= $j ?></th>
                                  <td><?= $this->Menu_model->get_userbyid($data->user_id)[0]->name ?></td>
                                  <td><?= $data->date ?></td>
                                  <td><?= $data->request_remarks ?></td>
                                  <td>
                                    <?php
                                    if($data->approvel_status == ''){ ?>
                                    <span class="p-1 bg-warning mr-2">Pending</span>
                                    <?php }else if($data->approvel_status == 'Approved'){ ?>
                                    <span class="p-1 bg-success mr-2">Approved</span>
                                    <?php }else{ ?>
                                    <span class="p-1 bg-danger mr-2">Reject</span>
                                    <?php }?>
                                  </td>
                                  <td><?=$data->remarks ?></td>
                                  <td>
                                    
                                    <?php
                                    if($data->approvel_status == ''){ ?>
                                    
                                    <div>
                                      <p><a href="<?=base_url();?>Menu/TodaysTaskapprove/<?= $data->id?>/Approve" class="btn btn-success mr-2" onclick="return confirm('Are you sure you want to Approved id?');" >Approve</a></p>
                                      <p><button type="button" class="btn btn-primary"  onclick="Reject(<?= $j ?>,<?= $data->id?>,'Reject')">Reject</button></p>
                                    </div>
                                    
                                    <?php }else if($data->approvel_status == 'Approved'){ ?>
                                    <span class="p-1 bg-success mr-2">Approved</span>
                                    <?php }else{ ?>
                                    <span class="p-1 bg-danger mr-2">Reject</span>
                                    <?php }?>
                                    
                                  </td>
                                </tr>
                                <?php $j++; } ?>
                              </tbody>
                              
                            </table>
                          </div>
                        </div>
                        <br>
                      </div>
                    </div>
                    
                    <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <form action="<?=base_url();?>Menu/TodaysTaskReject" method="post" >
                              <input type="hidden" id="rejectid" value="" name="reject">
                              <div class="mb-3 mt-3">
                                <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                              </div>
                              <div class="form-group text-center">
                                <button type="submit" class="btn btn-success mt-2">Submit</button>
                              </div>
                            </form>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type='text/javascript'>
        function RejectButton(mid,id,val){
        $('#exampleModalCenter'+mid).modal('show');
        $('#exampleModalCenter'+mid+' #rejectid').val(id);
        }
        
        function Reject(mid,id,val){
        // alert(mid);
        // alert('#exampleModalCenterdata'+mid);
        $('#exampleModalCenterdata').modal('show');
        $('#rejectid').val(id);
        // $('#exampleModalCenterdata'+mid).modal('show');
        // $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
        }
        </script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
      <!-- </section> -->
      <!-- /.content -->
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
  $("#exampledata").DataTable({
  "responsive": false, "lengthChange": false, "autoWidth": false,
  "buttons": ["copy", "csv", "excel", "pdf", "print"]
  }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
  
  </script>
</body>
</html>
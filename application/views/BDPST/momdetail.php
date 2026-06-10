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
            <?php if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            
            <?php endif; ?>
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">MOM Detail</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <h4><?php $uid=$user['user_id']; ?></h4>
                  </ol>
                  </div><!-- /.col -->
                  </div><!-- /.row -->
                  </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->
                <?php
                $bd = $this->Menu_model->get_userbyaid($uid);
                ?>
                <!-- Main content -->
                <section class="content">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-12">
                        <div>
                          <form class="p-3" method="POST" action="momdetail"><input type="date" name="sdate" class="mr-2" value="<?=$sd?>"><input type="date" name="edate" class="mr-2" value="<?=$ed?>">
                        <button type="submit" class="bg-primary text-light">Filter</button></form>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="body-content">
                            <div class="page-header">
                              <fieldset>
                                <div class="table-responsive">
                                  <div class="table-responsive">
                                    <div class="pdf-viwer">
                                      <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                          <tr>
                                            <th>Sr. no</th>
                                            <th>Company Name</th>
                                            <th>BD Name</th>
                                            <th>MOM Date</th>
                                            <th>MOM</th>
                                            <th>PST</th>
                                            <th>Approved Status</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                          </tr>
                                        </thead>

                                        <tbody>
                                          <?php
                                          $momd = $this->Menu_model->get_mombtdate($sd,$ed,$uid,0);
                                          $j =1;
                                          foreach($momd as $momd){
                                          ?>
                                          <tr>
                                            <td><?=$j?></td>
                                            <td><?=$momd->cname?></td>
                                            <td><?=$momd->name?></td>
                                            <td><?=date('d-m-Y h:i A', strtotime($momd->updateddate));?></td>
                                            <td><?=$momd->mom?></td>
                                            <td><?php if($momd->pst){echo $momd->pst;}else{echo 'PST Not Assign';}?></td>
                                            <td>
                                              <?php
                                              if($momd->mom_approved == ''){ ?>
                                              <span class="p-1 bg-warning mr-2">Pending</span>
                                              <?php }else if($momd->mom_approved == 'Approved'){ ?>
                                              <span class="p-1 bg-success mr-2">Approved</span>
                                              <?php }else{ ?>
                                              <span class="p-1 bg-danger mr-2">Reject</span>
                                              <?php }?>
                                            </td>
                                            <td>
                                              <?= $momd->mom_remarks ?>
                                            </td>
                                            <td>
                                        
                                            <?php
                                              if($momd->mom_approved == ''){ ?>
                                              <div>
                                                <p><a href="<?=base_url();?>Menu/momapproved/<?= $momd->id?>/Approve" class="btn btn-success mr-2" onclick="return confirm('Are you sure you want to Approved id?');" >Approve</a></p>
                                                <p><button type="button" class="btn btn-primary"  onclick="Reject(<?= $j ?>,<?= $momd->id?>,'Reject')">Reject</button></p>
                                              </div>
                                              
                                              <?php }else if($momd->mom_approved == 'Approved'){ ?>
                                              <span class="p-1 bg-success mr-2">Approved</span>
                                              <?php }else{ ?>
                                              <span class="p-1 bg-danger mr-2">Reject</span>
                                              <?php }?>
                                            </td>
                                          </tr>
                                          <?php  $j++; } ?>
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                  </form>            <!--END OF FORM ^^-->
                                </fieldset>
                                
                                
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
                          <form action="<?=base_url();?>Menu/momReject" method="post" >
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
              "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
              }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
              </script>
              <script type='text/javascript'>
              function RejectButton(mid,id,val){
              $('#exampleModalCenter'+mid).modal('show');
              $('#exampleModalCenter'+mid+' #rejectid').val(id);
              }
              
              function Reject(mid,id,val){
              $('#exampleModalCenterdata').modal('show');
              $('#rejectid').val(id);
              }
              </script>
            </body>
          </html>
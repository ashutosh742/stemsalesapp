<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title> Planner Task Approvel Page | STEM APP | WebAPP</title>
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
                <h4 class="m-0">Planner Task Details Details</h4>
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
                          <form class="p-3" method="POST" action="PlannerTaskApprovelPage">
                            <input type="date" name="sdate" class="mr-2" value="<?=$sdate?>">
                        <button type="submit" class="bg-primary text-light">Filter</button></form>
                        </form> 
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="body-content">
                            <div class="page-header">
                            <?php 
                            
                            $getbdteam = $this->Menu_model->get_userbyaaid($uid);
                            unset($getbdteam[0]);
                            // $totalDone =$this->Menu_model->get_ttbydone(100197,$sdate);
                            // echo "<pre>";
                            // print_r($totalDone);
                             ?>
                              <fieldset>
                                 <div class="card">
                                    <div class="text-center p-2 bg-info">
                                       <h5><i> Planner Task </i></h5>
                                       <p><i><b><?= $sdate ?></b></i></p>
                                    </div>
                                    <br>

                                <div class="table-responsive">
                                  <div class="table-responsive">
                                    <div class="pdf-viwer">
                                      <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                          <tr>
                                            <th>Sr. no</th>
                                            <th>User Name</th>
                                            <!-- <th>All </th> -->
                                            <th>Call</th>
                                            <th>Email</th>
                                            <th>Meeting</th>
                                            <th>WA</th>
                                            <th>Research</th>
                                            <th>Social Networking</th>
                                            <th>Social Activity</th>
                                            <th>MOM</th>
                                            <th>Proposal</th>
                                            <th>Visit Meeting</th>
                                            <th>Check Task</th>
                                            <th>Total Minutes</th>
                                            <th>Action</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                    <?php 
                                    $i=1;
                                    foreach($getbdteam as $team): 
                                 
                                        $ttbydc = $this->Menu_model->get_ttbyd($team->user_id,$sdate);
                                        $barg=$this->Menu_model->get_bargdetail1($team->user_id,$sdate);
                                        $totalttaskdata =$this->Menu_model->get_totaltdetails($team->user_id,$sdate);
                                      
                                        $taskplanmincount = 0;

                                        foreach($totalttaskdata as $taskdata){ 
                                            $actiontype_id = $taskdata->actiontype_id;
                                            if($actiontype_id ==5 || $actiontype_id ==8 || $actiontype_id ==9 || $actiontype_id ==1 || $actiontype_id ==10 || $actiontype_id ==15){
                                                $taskplanmincount = $taskplanmincount+5;
                                            }else if($actiontype_id ==2 || $actiontype_id ==6){
                                                $taskplanmincount = $taskplanmincount+10;
                                            }else if($actiontype_id ==3 || $actiontype_id ==4 || $actiontype_id ==12){
                                                $taskplanmincount = $taskplanmincount+30;
                                            }else if($actiontype_id ==7){
                                                $taskplanmincount = $taskplanmincount+15;
                                            }else if($actiontype_id ==11 || $actiontype_id ==13 || $actiontype_id ==14){
                                                $taskplanmincount = $taskplanmincount+2;
                                            }else{
                                                $new_datetime = $taskplanmincount;
                                            }
                                        }

                                    ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td> <a href="<?=base_url();?>Menu/userDetails/<?=$team->user_id ?>"> <?= $team->name ?></a></td>
                                            <!-- <td><?=$ttbydc[0]->ab?></td> -->
                                            <td><?=$ttbydc[0]->a?></td>
                                            <td><?=$ttbydc[0]->b?></td>
                                            <td><?=$ttbydc[0]->c?></td>
                                            <td><?=$ttbydc[0]->e?></td>
                                            <td><?=$ttbydc[0]->h?></td>
                                            <td><?=$ttbydc[0]->i?></td>
                                            <td><?=$ttbydc[0]->j?></td>
                                            <td><?=$ttbydc[0]->f?></td>
                                            <td><?=$ttbydc[0]->g?></td>
                                            <td>
                                            <?php
                                                $bm=0;
                                                foreach($barg as $bgs){
                                                    $bs = $bgs->status;
                                                    if($bs=='Pending'){$bm++;}
                                                }
                                                ?>
                                                <?=$bm?>
                                            </td>
                                            <td>
                                                <a href="<?=base_url();?>Menu/CheckTaskDetailsByUser/<?=$team->user_id?>/<?= $sdate; ?>"> Show </a>
                                            </td>
                                            <td>
                                                    <?php
                                                    $hours = floor($taskplanmincount / 60);
                                                    $remainingMinutes = $taskplanmincount % 60;
                                                    echo "$hours hours and $remainingMinutes minutes.";
                                                     ?>
                                                </td>
                                              <td>
                                               
                                            <div>
                                                <p><a href="<?=base_url();?>Menu/plannertaskApproved/<?= $momd->id?>/Approve" class="btn btn-success mr-2" onclick="return confirm('Are you sure you want to Approved it?');" >Approve</a></p>
                                                <p><button type="button" class="btn btn-primary"  onclick="Reject(<?= $j ?>,<?= $momd->id?>,'Reject')">Reject</button></p>
                                              </div>
                                            </td>
                                        </tr>
                                        <?php $i++; endforeach; ?>
                                    
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>   
                              </div>

                              <hr>


                              



                              <hr>


                              <div class="table-responsive">
                                <div class="card bg-success p-3 text-center">
                                    <h5>Total Complete Task</h5>
                                </div>
                                  <div class="table-responsive">
                                    <div class="pdf-viwer">
                                      <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                          <tr>
                                            <th>Sr. no</th>
                                            <th>User Name</th>
                                            <!-- <th>All </th> -->
                                            <th>Call</th>
                                            <th>Email</th>
                                            <th>Meeting</th>
                                            <th>WA</th>
                                            <th>Research</th>
                                            <th>Social Networking</th>
                                            <th>Social Activity</th>
                                            <th>MOM</th>
                                            <th>Proposal</th>
                                            <th>Visit Meeting</th>
                                            <th>Check Task</th>
                                            <th>Total Minutes</th>
                                            <th>Action</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                    <?php 
                                    $i=1;
                                    foreach($getbdteam as $team): 
                                
                                        $ttbydc = $totalDone =$this->Menu_model->get_ttbydone($team->user_id,$sdate);;
                                        $barg=$this->Menu_model->get_bargdetail1($team->user_id,$sdate);
                                        $totalttaskdata =$this->Menu_model->get_totaltdetailsDone($team->user_id,$sdate);
                                      
                                        $taskplanmincount = 0;

                                        foreach($totalttaskdata as $taskdata){ 
                                            $actiontype_id = $taskdata->actiontype_id;
                                            if($actiontype_id ==5 || $actiontype_id ==8 || $actiontype_id ==9 || $actiontype_id ==1 || $actiontype_id ==10 || $actiontype_id ==15){
                                                $taskplanmincount = $taskplanmincount+5;
                                            }else if($actiontype_id ==2 || $actiontype_id ==6){
                                                $taskplanmincount = $taskplanmincount+10;
                                            }else if($actiontype_id ==3 || $actiontype_id ==4 || $actiontype_id ==12){
                                                $taskplanmincount = $taskplanmincount+30;
                                            }else if($actiontype_id ==7){
                                                $taskplanmincount = $taskplanmincount+15;
                                            }else if($actiontype_id ==11 || $actiontype_id ==13 || $actiontype_id ==14){
                                                $taskplanmincount = $taskplanmincount+2;
                                            }else{
                                                $new_datetime = $taskplanmincount;
                                            }
                                        }

                                    ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td> <a href="<?=base_url();?>Menu/userDetails/<?=$team->user_id ?>"> <?= $team->name ?></a></td>
                                            <!-- <td><?=$ttbydc[0]->ab?></td> -->
                                            <td><?=$ttbydc[0]->a?></td>
                                            <td><?=$ttbydc[0]->b?></td>
                                            <td><?=$ttbydc[0]->c?></td>
                                            <td><?=$ttbydc[0]->e?></td>
                                            <td><?=$ttbydc[0]->h?></td>
                                            <td><?=$ttbydc[0]->i?></td>
                                            <td><?=$ttbydc[0]->j?></td>
                                            <td><?=$ttbydc[0]->f?></td>
                                            <td><?=$ttbydc[0]->g?></td>
                                            <td>
                                            <?php
                                                $bm=0;
                                                foreach($barg as $bgs){
                                                    $bs = $bgs->status;
                                                    if($bs=='Pending'){$bm++;}
                                                }
                                                ?>
                                                <?=$bm?>
                                            </td>
                                            <td>
                                                <a href="<?=base_url();?>Menu/CheckTaskDetailsByUser/<?=$team->user_id?>/<?= $sdate; ?>"> Show </a>
                                            </td>
                                            <td>
                                                <?php
                                                $hours = floor($taskplanmincount / 60);
                                                $remainingMinutes = $taskplanmincount % 60;
                                                echo "$hours hours and $remainingMinutes minutes.";
                                                    ?>
                                                </td>
                                              <td>
                                               
                                            <div>
                                               <span class="bg-success p-2">complete</span>
                                              </div>
                                            </td>
                                        </tr>
                                        <?php $i++; endforeach; ?>
                                    
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>   
                              </div>








                            </div>
                            </fieldset>
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
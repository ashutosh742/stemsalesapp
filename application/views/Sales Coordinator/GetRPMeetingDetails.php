<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Current Day Activity On APP | STEM APP | WebAPP</title>
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
      .content {
      margin: 20px;
      padding: 20px;
      border: 1px solid #ccc;
      }
      .card.card-fordetails{
      height: 100px;
      }
      .card.taskcntcard {
      height: 80px;
      align-items: center;
      justify-content: center;
      display: flex;
      box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
      }
      @media print {
      #printPage {
      display: none; /* Hides the print button during printing */
      }
      }
      table.table.table-striped {
    box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;
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
        <!-- /.content-header -->
        <?php 
          $bd = $this->Menu_model->get_userbyaid($uid);
          ?>
        <!-- Main content -->
        <section class="content" id="pdf-content">
          <div class="text-right mt-2">
            <button class="btn btn-info" id="printPage">Print Page</button> <br>
          </div>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12 text-center p-2">
                <div class="card-header bg-primary">
                    <?php 
                     $readableKey_reqType = ucwords(str_replace('_', ' ', $reqType));
                    $reqType ?>
                  <h4 class="text-center"><?=$slsctteam?> <?=$readableKey_reqType ?> Reports</h4>
                  <h5><?= $tarDate;?></h5>
                </div>
              </div>
            </div>
   
            <div class="card p-3">
              <div class="row">
                <?php  //  dd($getAllUserData); ?>
             
                <hr>
                <?php 
                  if($slsctteam !== 'Team'){ 
                  $getslsctuserData = $this->Menu_model->get_userbyid($targetuid);

                  ?>
                <div class="col-md-12">
                  <div class="card text-center p-2" style="background: #ff006f; font-weight: 500; color: white;">
                    <h3>Name : <?= $getslsctuserData[0]->name ?></h3>
                  </div>
                </div>
                <?php }else{ ?>
                    <div class="col-md-12">
                  <div class="card text-center p-2" style="background: #ff006f; font-weight: 500; color: white;">
                    <h3>Team</h3>
                  </div>
                </div>
                <?php }?>
         
                <div class="table-responsive">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr class="thead-dark">
                        <th>Sr. no</th>
                        <th>Company Name</th>
                        <th>Action Name</th>
                        <!-- <th>Action Taken</th>
                          <th>Purpose Achieved</th> -->
                        <th>Status</th>
                        <th>Task Time</th>
                        <th>Task Appointment time</th>
                        <!-- <th>Task initiated time</th>
                          <th>Task Update time</th> -->
                        <th>Plan By</th>
                        <th>Filter Used</th>
                        <th>Task Work Status</th>
                        <th>Action Status</th>
                        <th>Approved By</th>
                        <th>Action</th>

                        <th>Initiate Time</th>
                        <th>Initiate Location</th>

                        <th>Start Time</th>
                        <th>Start Location</th>

                        <th>Close Time</th>
                        <th>Close Location</th>
                        <th>image</th>
                        <!-- <th>View MOM Details</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        $i = 1;
                        foreach ($totalttask as $taskdata) { 
                        
                            $taid = $taskdata->actiontype_id;
                            $tblId = $taskdata->id;
                            $taid = $this->Menu_model->get_actionbyid($taid);
                            $time = $taskdata->appointmentdatetime;
                            $reminder = $taskdata->reminder;
                            $rimby = $taskdata->reminderby;
                            $rimat = $taskdata->reminderat;
                            $assignedto_id = $taskdata->assignedto_id;
                            $status = $taskdata->approved_status;
                            $self_assign = $taskdata->self_assign;
                            $approver = $taskdata->approved_by;
                            $filter_by = json_decode($taskdata->filter_by, true);
                            $selectby = $taskdata->selectby;
                            $reviewtype = $taskdata->reviewtype;
                            $rimbyname = $this->Menu_model->get_userbyid($rimby);
                            $time = date('h:i a', strtotime($time));
                            $taskuser = $this->Menu_model->get_userbyid($assignedto_id);
                            $taskuname = $taskuser[0]->name;
                        
                            $actontaken = $taskdata->actontaken;
                            $purpose_achieved = $taskdata->purpose_achieved;
                            $remarks = $taskdata->remarks;
                            $initiateddt = $taskdata->initiateddt;
                            $updateddate = $taskdata->updateddate;


                            $updateddate = $taskdata->updateddate;

                            $initphoto = $taskdata->initphoto;
                            $cphoto = $taskdata->cphoto;

                            $initiateTime = $taskdata->initiateTime;
                            $initiateLat = $taskdata->initiateLat;
                            $initiateLongi = $taskdata->initiateLongi;

                            $startm = $taskdata->startm;
                            $slatitude = $taskdata->slatitude;
                            $slongitude = $taskdata->slongitude;

                            $closem = $taskdata->closem;
                            $clatitude = $taskdata->clatitude;
                            $clongitude = $taskdata->clongitude;

                            $barginmeeting_id    = $taskdata->barginmeeting_id;
                            $mom_id              = $taskdata->mom_id;
                            $mom_approved_status = $taskdata->approved_status;

                            

              
                        
                        ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td><?= $taskdata->compname ?></td>
                        <td><?= $taid[0]->name ?></td>
                        <?php /* <td><?= $actontaken ?></td>
                        <td><?= $purpose_achieved ?></td>
                        */ ?>
                        <td><span class="p-3" style="color:<?= $taskdata->color ?>;"><?= $taskdata->csname ?></span></td>
                        <td><?= $time ?></td>
                        <td><?= $taskdata->appointmentdatetime ?></td>
                        <?php /* <td><?= $initiateddt ?></td>
                        <td><?= $updateddate ?></td>
                        */ ?>
                        <td><?= $taskuname ?></td>
                        <td><?php 
                          if(sizeof($filter_by) > 0){
                          foreach ($filter_by as $key => $value) {
                            if($key == 'Plan_BY'){
                              echo $value."<br/>";
                             
                            }elseif($key == 'Filter_By'){
                              echo $value."<br/>";
                            }else{
                            if($key == 'comp_status'){
                              $tstatus = $this->Menu_model->get_statusbyid($value);
                              $tstatusname = $tstatus[0]->name;
                              echo "<span class='p-1'>Status&nbsp;:&nbsp;".$tstatusname."</span><br/>";
                            }elseif($key == 'task'){
                               
                              $taction = $this->Menu_model->get_actionbyid($value);
                              $tactionname = $taction[0]->name;
                              echo "<span class='p-1'>Task&nbsp;:&nbsp;".$tactionname."</span><br/>";
                            }elseif($key == 'taskActionbyuser'){
                                
                              echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                            }else{
                                echo $selectby;
                              }
                            }
                          }
                          }else{
                            if(!is_null($reviewtype)){
                                echo $reviewtype." Review";
                            }else if($self_assign == 4){ 
                                echo "AutoTask";
                                 }else{
                                   if($taid == 3 || $taid == 4){
                                        echo "Task Actions";
                                   }else{
                                    echo "Task Actions";
                                   }
                                 }
                          }
                          ?></td>
                        <td>
                          <?php 
               
                            if($taskdata->nextCFID == 0){?>
                          <span class="p-1 bg-warning mr-2">Pending</span>
                          <?php }else{ ?>
                          <span class="p-1 bg-success mr-2">Complete</span>
                          <?php } ?>
                        </td>
                        <td>
                          <span class="p-1 bg-<?= ($status == 1) ? 'success' : (($status == '') ? 'warning' : 'danger');?> mr-2">
                          <?= ($status == 1) ? 'Approved' : (($status == '') ? 'Pending' : 'Rejected'); ?>
                          </span>
                        </td>
                        <td><?php
                          if($approver ==''){ ?>
                          <span class="p-1 bg-warning mr-2">Pending</span>
                          <?php }else{
                            if($approver == 'System'){ ?>
                          <span class="p-1 bg-success mr-2"><?= $approver ?></span>
                          <?php }else{
                            $userData = $this->Menu_model->get_userbyid($approver);
                            $name = $userData[0]->name;
                            echo $name;
                            }
                            }
                            ?>
                        </td>
                        <td>
                          <?php if($status ==''){?>
                          <div>
                            <span class="p-1 bg-warning mr-2">Pending</span>
                          </div>
                          <?php }elseif($status == 0){
                            if($self_assign == ''){ ?>
                          <div>
                            <button type="button" class="btn btn-primary" onclick="window.location.href='<?= base_url(); ?>Menu/AssignTaskById/<?= $tblId ?>/<?= $taskdate ?>'">Assign Task</button> 
                            <hr>
                            <button type="button" class="btn btn-secondary" id="self_assign" style="margin-left: 10px;" onclick="window.location.href='<?= base_url(); ?>Menu/selfAssign/<?= $tblId ?>/1/<?= $taskdate ?>'" <?= ($self_assign == 1) ? 'disabled' : '' ?>>Self Assign</button>
                          </div>
                          <?php  }else if($self_assign == 1){ ?>
                          <span class="p-1 bg-success mr-2">Admin&nbsp;Created&nbsp;Self-Assign&nbsp;Request</span>
                          <?php } else if($self_assign == 2){?>
                          <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;admin!</span>
                          <?php }else if($self_assign == 3){ ?>
                          <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;user!</span>
                          <?php }else if($self_assign == 4){ ?>
                          <span class="p-1 bg-success mr-2">Task&nbsp;Approved&nbsp;&nbsp;by&nbsp;System!</span>
                          <?php }?>
                          <?php }elseif($status == 1){ ?>
                          <?php if($self_assign == 4){ ?>
                          <span class="p-1 bg-success mr-2">This&nbsp;is&nbsp;Autotask!</span>
                          <?php }else{ ?>
                          <span class="p-1 bg-success mr-2">Approved</span> 
                          <?php }  ?>
                          <?php }?>
                        </td>
                        <td><?= $initiateTime ?></td>
                        <td>
                        <iframe id="endMap" src="https://maps.google.com/?q=<?= $initiateLat ?>,<?= $initiateLongi ?>&t=k&z=13&ie=UTF8&iwloc=&output=embed" width="150px" height="100%" frameborder="0" style="border:0" allowfullscreen> </iframe>
                         </td>
                        <td><?= $startm ?></td>
                        <td>
                        <iframe id="endMap" src="https://maps.google.com/?q=<?= $slatitude ?>,<?= $slongitude ?>&t=k&z=13&ie=UTF8&iwloc=&output=embed" width="150px" height="100%" frameborder="0" style="border:0" allowfullscreen> </iframe>
                         </td>
                         <td><?= $closem ?></td>
                        <td>
                        <iframe id="endMap" src="https://maps.google.com/?q=<?= $clatitude ?>,<?= $clongitude ?>&t=k&z=13&ie=UTF8&iwloc=&output=embed" width="150px" height="100%" frameborder="0" style="border:0" allowfullscreen> </iframe>
                         </td>
                        <td>
                            <?php if($cphoto !=='uploads/day/'){ ?>
                                
                            <img src="<?=base_url().$cphoto?>" width="200px" height="200px" class="img-fluide" alt="">
                            <a href="<?=base_url().$cphoto?>" target="_BLANK">view</a>
                            <?php }else{ ?>
                                <img id="modalPhoto"  src="https://stemapp.in/uploads/day/17381481267637508691630072467188.jpg" width="200px" height="200px" alt="Photo" class="img-fluid">
                                <a href="<?=base_url().$cphoto?>" target="_BLANK">view</a>
                            <?php }?>
                         </td>

                         <?php /*
                        <td>
                        <?php 
                        echo $taskdata->mom_id;
                        if(!is_null($taskdata->mom_id)){ ?>
                        <a href="<?=base_url();?>/Management/MomDataCheckInAdmin/<?=$taskdata->mom_id?>/<?=$taskdata->cid?>">view</a>
                        <?php }else{ ?>
                            <span class="bg-warning p-1">Pendng</span>
                        <?php } ?>
                         </td>
                         */ ?>
                        <?php $i++; } ?>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <hr/>
              </div>
            </div>
          </div>
      </div>
      </section>
    </div>
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
      </div>
    </footer>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
 
    <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
        })
    </script>
  </body>
</html>
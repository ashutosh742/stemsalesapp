<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Mom Data | STEM APP | WebAPP</title>
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
      .content-wrapper>.content {
    background: blanchedalmond;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <?php 
                // dd($momdata);
                ?>
    
                <div class="card mt-2">
                  <div class="card-header bg-info">
                    <h3 class="text-center">MINUTES OF MEETING (MoM Data)</h3>
                  </div>
           
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                            <div class="table-responsive">
                              <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th>S.No</th> 
                                      <th>BD Name</th>
                                      <th>Company Name</th> 
                                      <th>Current Status</th> 
                                      <th>Action Id</th> 
                                      <th>Action taken</th> 
                                      <th>Meeting done with discision maker of the company</th> 
                                      <th>MOM Submit Date</th>
                                      <th>RP MOM Remarks</th>
                                      <th>View</th>
                                      <th>Approved Status</th>
                                      <!-- <th>Action</th> -->
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                      $momdata = $this->Management_model->getAllPendngBDMoMData($suid,$tdate);
                                        foreach($momdata as $dt){

                                           $getTblCallData = $this->Management_model->get_BDMoM_TBL_Call_Data($dt->tid);
                                     
                                           $fwd_date = $getTblCallData[0]->fwd_date;
                                           $appointmentdatetime = $getTblCallData[0]->appointmentdatetime;
                                           $plandt = $getTblCallData[0]->plandt;
                                           $updateddate = $getTblCallData[0]->updateddate;
                                           $remarks = $getTblCallData[0]->remarks;

                                           $utype = $this->Menu_model->get_userbyid($dt->user_id);
                                           $uname = $utype[0]->name;
                                           $getcmp = $this->Menu_model->get_cmpbyinid($dt->init_cmpid);
                                           $compname = $getcmp[0]->compname;
                                           $ccstatus = $this->Menu_model->get_statusbyid($dt->ccstatus);
                                           $ccstatusname = $ccstatus[0]->name;
                                           $action_name = $this->Menu_model->get_actionbyid($dt->action_id);
                                           $action_name = $action_name[0]->name;

                                           
                                           if($dt->approved_by !== ''){

                                            $approvedid = $this->Menu_model->get_userbyid($dt->approved_by);
                                            $approved_byname = $approvedid[0]->name;
                                            $approved_status = $dt->approved_status;
                                            $approved_date = $dt->approved_date;
                                          
                                            $reject_remarks = $dt->reject_remarks;
                                            $edit_cnt = $dt->edit_cnt;
                                            
                                            
                                           }
                                           
                                           


                                       ?>
                                    <tr>
                                      <td><?=$i?></td>
                                      <td><?=$uname?></td>
                                      <td><?=$compname?></td>
                                      <td><?=$ccstatusname ?></td>
                                      <td><?=$action_name?></td>
                                      <td><?=$dt->actontaken?></td>
                                      <td><?=$dt->meetingdonewinitiator?></td>
                                      <td><?=$updateddate?></td>
                                      <td><?= $remarks ?></td>
                                      <td> 
                                   
                                        <a href="<?=base_url().'Management/MomDataDisplay/'.$dt->user_id.'/'.$tardate.'/'.$dt->id;?>"> View Mom Details
                                    </a>
                                </td>
                                <td>
                                    <?php  
                                  
                                    if($dt->approved_by !== ''){ ?>
                                    <b>Approved Status :</b> <?= $approved_status ?> <hr>
                                    <?php if($approved_status =='Reject'){$approved_statusname = 'Reject';}else{$approved_statusname = 'Approved';} ?>
                                    <b><?= $approved_statusname ?> By :</b> <?= $approved_byname ?> <hr>
                                    <b><?= $approved_statusname ?> Date :</b> <?= $approved_date ?> <hr>
                                    <b>Remarks :</b> <?= $reject_remarks ?>
                                    <?php }else{
                                        echo "<span class='bg-warning p-1'>Pending</span>";
                                    } ?>
                                </td>
                                <?php /*?><td>
                                      <?php  
                                     
                                      if($dt->approved_by == ''){
                                         ?>
                                        <!-- <div>
                          
                                            <p><button type="button" class="btn btn-success" onclick="MomApprove(<?= $i ?>,<?= $dt->id?>,'Approve')">Approve</button></p>
                                            <p><button type="button" class="btn btn-primary" onclick="Reject(<?= $i ?>,<?= $dt->id?>,'Reject')">Edit</button></p>
                                            <p><button type="button" class="btn btn-primary" id="rPtoNoRPConvert<?= $dt->tid?>" onclick="RPtoNoRPConvert(<?= $dt->tid?>,<?= $dt->id?>,'RPtoNoRPConvert')">RP to No RP Convert</button></p>
                                        </div> -->
                                        <?php }else{ 
                                          if($approved_status =='Reject'){ ?>
                                              <span class="bg-danger p-2" >Reject</span>
                                        <?php }else{ ?>
                                             <span class="bg-success p-2" >Approved</span>
                                             <?php } ?>
                                      <?php  } ?>
                                    </td> <?php */ ?>
                                    
                                    </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
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
                          <form action="<?=base_url();?>Management/MomRejectByUserAdmin" method="post" >
                            <input type="hidden" id="rejectid" value="" name="reject">
                            <input type="hidden" value="<?=$suid?>" name="suid">
                            <input type="hidden" value="<?=$tardate?>" name="tardate">
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




                  <div class="modal fade" id="exampleModalCenterdataApprove" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">MOM Remarks</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <form action="<?=base_url();?>Management/MomApprovedByUserAdmin" method="post" >
                            <input type="hidden" id="mom_id" value="" name="mom_id">
                            <input type="hidden" value="<?=$suid?>" name="suid">
                            <input type="hidden" value="<?=$tardate?>" name="tardate">
                            <input type="text" class="form-control" readonly value="Is this mom is Right" name="right_remarks">
                            <hr>
                            <select class="form-control" name="mom_yes_no">
                                <option selected disabled>Select on</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                               
                              </select>

                            <div class="form-group text-center">
                              <button type="submit" class="btn btn-success mt-2">Submit</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>



    </section>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'></script>
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
        "buttons": ["csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>


<script type='text/javascript'>      
 function Reject(mid,id,val){
              $('#exampleModalCenterdata').modal('show');
              $('#rejectid').val(id);
              }

 function MomApprove(mid,id,val){
              $('#exampleModalCenterdataApprove').modal('show');
              $('#mom_id').val(id);
              }
 
function RPtoNoRPConvert(id,mom_id,val){

              let text = "Do You Want to Change RP to No RP Meeting.";
                if (confirm(text) == true) {
                  text = "You pressed OK!";
      
                  var hideval = "#rPtoNoRPConvert"+id;
                  $.ajax({
                    url:'<?=base_url();?>Management/Change_RP_To_No_RP',
                    type: "POST",
                    data: {
                    tid: id,
                    mom_id: mom_id
                    },
                    cache: false,
                    success: function a(result){
                      console.log(result);
                      if(result ==1){
                        $(hideval).hide();
                        location.reload();
                      }else{
                        alert('Something Wrong !');
                      }
                    }
                    });
                } else {
                  text = "You canceled!";
                  alert(text);
                }
              }
            </script>
  </body>
</html>
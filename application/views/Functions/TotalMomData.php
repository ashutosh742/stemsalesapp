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
      .content-wrapper>.content {
    background: blanchedalmond;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php $this->load->view($dep_name.'/nav.php');?>
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                                                 <style>
th.text-capitalize:nth-child(1),
td:nth-child(1),
th.text-capitalize:nth-child(2),
td:nth-child(2) {
    position: sticky;
    left: 0;
    color: white;
    z-index: 10;
}

tbody tr td:nth-child(1),
tbody tr td:nth-child(2) {
    background-color: white;
    color: black;
}
</style>
    
                <div class="card mt-2" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="card-header text-center" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 >🖊️ Minutes of Meeting (Total Write MoM Data)</h3>
                    <small>The Total Write MoM Data ✍️ represents all meeting records created and documented by the team 📑. It covers every stage—pending, approved, or rejected—giving a complete picture of meeting outcomes 🗂️. This data ensures nothing is missed, promotes transparency 🤝, and provides a strong reference point 📌 for tracking discussions, decisions, and future action plans 🚀.</small>
                  </div>
           
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                              <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                        <th>🔢 S.No</th> 
                                        <th>👤 BD Name</th>
                                        <th>🏢 CID</th> 
                                        <th>🏢 Company Name</th> 
                                        <th>📊 Current Status</th> 
                                        <th>🆔 Action ID</th> 
                                        <th>🛠️ Action Taken</th> 
                                        <th>🤝 Meeting with Decision Maker</th> 
                                        <th>📅 MOM Submit Date</th>
                                        <th>📝 RP MOM Remarks</th>
                                        <th>👀 View</th>
                                        <th>👀 View Meeting Details</th>
                                        <th>✅ Approval Status</th>
                                        <th>⚡ Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                      $momdata = $this->Management_model->GetTotalSubmitBDMoMData($suid,$tardate);
                                        foreach($momdata as $dt){

                                           $getTblCallData = $this->Management_model->get_BDMoM_TBL_Call_Data($dt->tid);
                                     
                                           $fwd_date            = $getTblCallData[0]->fwd_date;
                                           $appointmentdatetime = $getTblCallData[0]->appointmentdatetime;
                                           $plandt = $getTblCallData[0]->plandt;
                                           $updateddate = $getTblCallData[0]->updateddate;
                                           $remarks = $getTblCallData[0]->remarks;
                                           $aftertask = $getTblCallData[0]->aftertask;
                                           
                                           $utype = $this->Menu_model->get_userbyid($dt->user_id);
                                           $uname = $utype[0]->name;
                                           $getcmp = $this->Menu_model->get_cmpbyinid($dt->init_cmpid);
                                           $cid = $dt->init_cmpid;
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
                                      <td>
                                        <a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?=$cid?>">
                                        <?=$cid?>
                                        </a>
                                      </td>
                                      <td>
                                    <a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?=$cid?>">
                                        <?=$compname?>
                                        </a>
                                    </td>
                                      <td><?=$ccstatusname ?></td>
                                      <td><?=$action_name?></td>
                                      <td><?=$dt->actontaken?></td>
                                      <td><?=$dt->meetingdonewinitiator?></td>
                                      <td><?=$updateddate?></td>
                                      <td><?= $remarks ?></td>
                                      <td> 
                                        <a target="_blank" class="btn btn-primary" href="<?=base_url().'Management/MomDataDisplay/'.$dt->user_id.'/'.$tardate.'/'.$dt->id;?>"> View Mom Details
                                    </a>
                                    </td> 
                                      <td> 
                                        <a target="_blank" class="btn btn-primary" href="<?=base_url().'Reports/ViewMeetingDetails/'.$aftertask?>"> View Meeting Details
                                      </a>
                                  </td>
                                <td>
                                    <?php  
                            
                                    if($dt->approved_by !== ''){ ?>
                                    <b>Approved Status :</b> <?= $dt->approved_status ?> |
                                    <?php if($dt->approved_status =='Reject'){$approved_statusname = 'Reject';}else{$approved_statusname = 'Approved';} ?>
                                    <b><?= $approved_statusname ?> By :</b> <?= $approved_byname ?> |
                                    <b><?= $approved_statusname ?> Date :</b> <?= $approved_date ?> |
                                    <b>Remarks :</b> <?= $reject_remarks ?>
                                    <?php } ?>
                                    <?php if($dt->approved_by == ''){
                                          echo "<span class='bg-warning p-2'> Pending </span>";
                                        } ?>
                                </td>
                                    <td>
                                      <?php if($dt->approved_status == 'Reject' && ($dt->edit_cnt == '' || $dt->edit_cnt == 0)){ ?>
                                        <div>
                                          <p><?php echo "<span class='bg-danger p-2'>Rejected</span>"; ?></p>
                                           
                                        </div>
                                        <?php }?>
                                        
                                        <?php if($dt->approved_status == 'Approved'){
                                          echo "<span class='bg-success p-2'> Approved&nbsp;Success </span>";
                                        } ?>
                                        <?php if($dt->approved_status == ''){
                                          echo "<span class='bg-warning p-2'> Pending </span>";
                                        } ?>
                                        <?php if($dt->edit_cnt > 0){
                                          echo "<span class='bg-success p-2'> MOM&nbsp;Updated</span>";
                                        } ?>
                                    </td>
                                     
                                    </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
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

<script>
    function statusupdate(val,blogid){
      var statusupdate = "#statusupdate"+blogid;
        var statusdata = $('#statusup'+blogid).val();
        $.ajax({
			url:'vendor/lengthstausupdate',
			type:'post',
			data:"id="+blogid+"&status="+statusdata,
			success:function(result){
                if(result == 0){
                    $(statusupdate).text('Pending');
                    $(statusupdate).css({"background-color": "red", "color": "white","padding": "5px"});
                }else{
                    $(statusupdate).text('Done');
                    $(statusupdate).css({"background-color": "Green", "color": "white","padding": "5px"});
                }
                // alert(result);
            }
        });
    }
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
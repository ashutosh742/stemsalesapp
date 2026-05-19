<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Check Add New Lead Data | STEM APP| WebAPP</title>
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
   
table.table.table-striped.table {
    /* background: linear-gradient(135deg, #EF7C8E, #FAE8E0, #B6E2D3, #D8A7B1); */
    background: linear-gradient(135deg, #EF7C8E 0%, #FAE8E0 33%, #B6E2D3 66%, #D8A7B1 100%);

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
            <?php 
              // Start task Data 
              $actontaken             = $taskData[0]->actontaken;
              $purpose_achieved       = $taskData[0]->purpose_achieved;
              $actiontype_id          = $taskData[0]->actiontype_id;
              $appointmentdatetime    = $taskData[0]->appointmentdatetime;
              $assignedto_id          = $taskData[0]->assignedto_id;
              $updateddate            = $taskData[0]->updateddate;
              $udetails               = $this->Menu_model->get_userbyid($assignedto_id);
              $username               = $udetails[0]->name;
              $action                 = $this->Menu_model->get_actionbyid($actiontype_id);
              $actionname             = $action[0]->name;
              // End task Data 
              ?>
            <div class="card text-center bg-info p-1">
              <h4>Check Add New Lead</h4>
            </div>
            <div class="card card-primary card-outline mt-4 font-weight-bold text-center">
              <div class="row">
                <div class="col-md-4">
                  <div class="card p-2 mt-3">
                    <span> Name: <?= $username; ?></span>
                    <hr>
                    <span> Task: <?= $actionname; ?></span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card p-2 mt-3">
                    <span> Action: <?= $actontaken; ?></span>
                    <hr>
                    <span> Purpose: <?= $purpose_achieved; ?></span>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card p-2 mt-3">
                    <span> Task DateTime: <?= $appointmentdatetime; ?></span> 
                    <hr>
                    <span> Task Update: <?= $updateddate; ?></span>
                  </div>
                </div>
              </div>
            </div>
            <?php 
              $compname         = $cData[0]->compname;
              $address          = $cData[0]->address;
              $website          = $cData[0]->website;
              $budget           = $cData[0]->budget;
              $city             = $cData[0]->city;
              $state            = $cData[0]->state;
              $country          = $cData[0]->country;
              $partnerType_id   = $cData[0]->partnerType_id;
              $partner          = $this->Menu_model->get_partnerbyid($partnerType_id);
              $partnername      = $partner[0]->name;

              if (is_numeric($city)) {
                $city = $this->Menu_model->get_citybyid($city);
              }elseif (ctype_alpha(str_replace(' ', '', $city))) {
                  $city = $city;
              }else {
                  $city =  $city;
              }

              if (is_numeric($state)) {
                $state = $this->Menu_model->get_statebyid($state);
              }elseif (ctype_alpha(str_replace(' ', '', $state))) {
                  $state = $state;
              }else {
                  $state =  $state;
              }

              if($city == ''){ $city = "<span class='bg-danger p-1'>City is not entered</span>";}
              if($state == ''){$state = "<span class='bg-danger p-1'>State is not entered</span>";}
              if($website == ''){$website = "<span class='bg-danger p-1'>Website is not entered</span>";}
              if($budget == ''){$budget = "<span class='bg-danger p-1'>Budget is not entered</span>";}
              if($address == ''){$state = "<span class='bg-danger p-1'>Address is not entered</span>";}
              if($country == ''){$country = "<span class='bg-danger p-1'>Country is not entered</span>";}
             

              ?>
            <div class="card mt-4">
              <div class="card text-center bg-info p-1">
                <h4>Company Details</h4>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="card p-2 mt-3">
                    <table class="table table-striped table1">
                      <tbody>
                        <tr>
                          <td>Company Name </td>
                          <td><?= $compname; ?></td>
                        </tr>
                        <tr>
                          <td>Company Address </td>
                          <td><?= $address; ?></td>
                        </tr>
                        <tr>
                          <td>Company Website </td>
                          <td><?= $website; ?></td>
                        </tr>
                        <tr>
                          <td>Company Budget </td>
                          <td><?= $budget; ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card p-2 mt-3">
                    <table class="table table-striped">
                      <tbody>
                        <tr>
                          <td>Country </td>
                          <td><?= $country; ?></td>
                        </tr>
                        <tr>
                          <td>State </td>
                          <td><?= $state; ?></td>
                        </tr>
                        <tr>
                          <td>City </td>
                          <td><?= $city; ?></td>
                        </tr>
                        <tr>
                          <td>Partner Type </td>
                          <td><?= $partnername; ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php  
                  $topspender       = $initData[0]->topspender;
                  $upsell_client    = $initData[0]->upsell_client;
                  $focus_funnel     = $initData[0]->focus_funnel;
                  $priorityc        = $initData[0]->priorityc;
                  $potential        = $initData[0]->potential;
                  $cluster_id       = $initData[0]->cluster_id;


                  if($topspender == ''){$topspender = "<span class='bg-danger p-1'>topspender is not marked</span>";}
                  if($upsell_client == ''){$upsell_client = "<span class='bg-danger p-1'>upsell_client is not marked</span>";}
                  if($focus_funnel == ''){$focus_funnel = "<span class='bg-danger p-1'>focus_funnel is not marked</span>";}
                  if($priorityc == ''){$priorityc = "<span class='bg-danger p-1'>priorityc is not marked</span>";}
                  if($potential == ''){$potential = "<span class='bg-danger p-1'>potential is not marked</span>";}
                  if($cluster_id == ''){
                    $cluster_id = "<span class='bg-danger p-1'>priorityc is not select</span>";
                  }else{
                    $cluster          = $this->Menu_model->GetClusterById($cluster_id);
                  }

                  
                  $clustername      = $cluster[0]->clustername;
                  $keycompany       = $initData[0]->keycompany;
                  $cstatus          = $initData[0]->cstatus;
                  $cstatus          = $this->Menu_model->get_statusbyid($cstatus);
                  $cstatusname      =  $cstatus[0]->name;
                  ?>
                <div class="col-md-6">
                  <div class="card p-2">
                    <table class="table table-striped">
                      <tbody>
                        <tr>
                          <td>Cureent Status </td>
                          <td><?= $cstatusname ?></td>
                        </tr>
                        <tr>
                          <td>Top Spender </td>
                          <td><?= $topspender; ?></td>
                        </tr>
                        <tr>
                          <td>Upsell Client  </td>
                          <td><?= $upsell_client; ?></td>
                        </tr>
                        <tr>
                          <td>Focus Funnel </td>
                          <td><?= $focus_funnel; ?></td>
                        </tr>
                        <tr>
                          <td>Key Client</td>
                          <td><?= $keycompany; ?></td>
                        </tr>
                        <tr>
                          <td>Potential Client </td>
                          <td><?= $potential; ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php 
                  $contactperson    = $ccData[0]->contactperson;
                  $emailid          = $ccData[0]->emailid;
                  $phoneno          = $ccData[0]->phoneno;
                  $designation      = $ccData[0]->designation;
                  $type             = $ccData[0]->type;
                  
                  ?>
                <div class="col-md-6">
                  <div class="card p-2">
                    <table class="table table-striped">
                      <tbody>
                        <tr>
                          <td>Contact Persion </td>
                          <td><?= $contactperson; ?></td>
                        </tr>
                        <tr>
                          <td>Email ID  </td>
                          <td><?= $emailid; ?></td>
                        </tr>
                        <tr>
                          <td>Phone Number </td>
                          <td><?= $phoneno; ?></td>
                        </tr>
                        <tr>
                          <td>Designation</td>
                          <td><?= $designation; ?></td>
                        </tr>
                        <tr>
                          <td>Contact Type </td>
                          <td><?= $type; ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

          
              <div>
                <center>
                    <p>
                    <a href="<?=base_url();?>Menu/CompanyApproredAfterNewLead/<?= $initData[0]->id;?>/Approve" class="btn btn-success mr-2" onclick="return confirm('Are you sure you want to Approved it?');" >Approve</a>
                            <button type="button" class="btn btn-primary" onclick="CompanyReject(<?= $initData[0]->id;?>,'Reject')">Need to Update</button>
                        </p>
                </center>
            </div>
              <hr>

            </div>


            <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Add Remarks For Update</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <form action="<?=base_url();?>Menu/UpdateCompanyAfterLeadCheck" method="post" >
                            <input type="hidden" id="rejectid" value="" name="rejectid">
                            <input type="hidden" value="Reject" name="status">
                            <div class="mb-3 mt-3">
                              <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                            </div>
                            <div class="form-group text-center">
                              <button type="submit" class="btn btn-success mt-2">Need to Update</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
          </div>
        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
     
        <script type='text/javascript'>      
            function CompanyReject(init_id,val){
                $('#exampleModalCenterdata').modal('show');
                $('#rejectid').val(init_id);
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
      "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
      
    </script>
  </body>
</html>
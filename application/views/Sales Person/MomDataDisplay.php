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
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card mt-2">
                  <div class="card-header bg-info">
                    <h3 class="text-center">MINUTES OF MEETING (MoM Details Data)</h3>
                  </div>
                  <?php
                //   dd($momdata); 
                  ?>
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
                                        <th>Sr No</th>
                                        <th>Question </th>
                                        <th>Answer</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                      

                                      $identify_school = $momdata[0]->identify_school;
                                      if($identify_school == 'yes'){
                                        $state = $momdata[0]->identify_school_state;
                                        $district = $momdata[0]->identify_school_district;
                                        $noofschool = $momdata[0]->no_of_school;

                                        $states = explode(',', $state);
                                        $districts = explode(',', $district);
                                        $schoolCounts = explode(',', $noofschool);

                                        // Combine into a single array
                                        $combinedArray = [];

                                        for ($i = 0; $i < count($states); $i++) {
                                            $combinedArray[] = [
                                                'identify_school_state' => $states[$i],
                                                'identify_school_district' => $districts[$i],
                                                'no_of_school' => $schoolCounts[$i]
                                            ];
                                        }
                                      }
                                   
                                     $combinedArraycnt = sizeof($combinedArray);
                                      // echo "<pre>";
                                      // print_r($combinedArray);
                                     
                                      if($key == 'identify_school_state'){
                                        $array = explode(',', $value);
                                        $k=1; foreach($array as $arr){ echo "<span>".$k .' - '.$arr."</span> <hr>"; $k++;}
                                      }
                                      if($key == 'identify_school_district'){
                                        $array = explode(',', $value);
                                        $k=1; foreach($array as $arr){ echo "<span>".$k .' - '.$arr."</span> <hr>"; $k++;}
                                      }
                                      if($key == 'no_of_school'){
                                        $array = explode(',', $value);
                                        $k=1; foreach($array as $arr){ echo "<span>".$arr."</span> <hr>"; $k++;}
                                      }



                                     foreach ($momdata as $values):

                                        unset($values->id);
                                        unset($values->reject_remarks);
                                        unset($values->cm_call_task);
                                        unset($values->bd_request_task);
                                        unset($values->school_visit_task);
                                        unset($values->pst_call_task);
                                        unset($values->pst_assign);
                                        unset($values->edit_cnt);

                                        $getTblCallData = $this->Management_model->get_BDMoM_TBL_Call_Data($values->tid);
                                     
                                        $fwd_date = $getTblCallData[0]->fwd_date;
                                        $appointmentdatetime = $getTblCallData[0]->appointmentdatetime;
                                        $plandt = $getTblCallData[0]->plandt;
                                        $updateddate = $getTblCallData[0]->updateddate;
                                        $remarks = $getTblCallData[0]->remarks;

                                        $utype = $this->Menu_model->get_userbyid($values->user_id);
                                        $uname = $utype[0]->name;
                                        $getcmp = $this->Menu_model->get_cmpbyinid($values->init_cmpid);
                                        $compname = $getcmp[0]->compname;
                                        $ccstatus = $this->Menu_model->get_statusbyid($values->ccstatus);
                                        $ccstatusname = $ccstatus[0]->name;
                                        $action_name = $this->Menu_model->get_actionbyid($values->action_id);
                                        $action_name = $action_name[0]->name;

                                        foreach ($values as $key => $value):
                                          if( $key == 'tid'){
                                            continue;
                                          }

                                          if($key == 'project_intervention_select'){if($value !== 'Others'){
                                              unset($values->project_intervention);
                                          }}
                                          
                                          if($key == 'client_has_adopted_select'){if($value == 'no'){
                                              unset($values->client_has_adopted);
                                          }
                                        }
                                          if($key == 'submit_proposal'){if($value == 'no'){
                                              unset($values->proposal_no_of_school);
                                              unset($values->proposal_of_budget);
                                              unset($values->proposal_of_location);
                                          }
                                        }
                                     
                                          if($key == 'identify_school')if($value == 'yes'){{
                                            unset($values->identify_school_district);
                                            unset($values->no_of_school);
                                          }}
                                          if($key == 'identify_school')if($value == 'no'){{
                                            unset($values->identify_school_state);
                                            unset($values->identify_school_district);
                                            unset($values->no_of_school);
                                          }}
                                        
                                          if($key == 'permission_letter'){
                                            if($value == 'no'){
                                              unset($values->permission_letter_rech);
                                              unset($values->Letter_organization_name);
                                              unset($values->Letter_organization_designation);
                                              unset($values->Letter_organization_location);
                                            }
                                          }
                                          if($key == 'client_int_school_visit'){
                                            if($value == 'no'){
                                              unset($values->client_int_type_project);
                                              unset($values->client_int_school_date);
                                              unset($values->client_int_school_state);
                                              unset($values->client_int_school_district);
                                              unset($values->client_int_no_of_school);
                                            }
                                          }

                                       
                                     ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?php 
                                            // echo $key;
                                            if($key == 'ccstatus'){echo "Current Company Status";}
                                            if($key == 'action_id'){echo "Action ID";}
                                            if($key == 'user_id'){echo "BD Name";}
                                            if($key == 'init_cmpid'){echo "Comapny Name";}
                                            if($key == 'actontaken'){echo "Action Taken";}
                                            if($key == 'meetingdonewinitiator'){echo "Meeting done with Initiator or influencer and decision maker of the company";}
                                            if($key == 'presentation'){echo "Presentation and pitching is done for which offering :";}
                                            if($key == 'project_intervention_select'){echo "What is the client's thematic Area for Project Intervention in the current financial Year";}
                                         
                                            if($key == 'project_intervention'){echo "What is the client's Other thematic Area for Project Intervention in the current financial Year";}
                                            
                                            if($key == 'client_has_adopted_select'){echo "Does the client has adopted any schools ?";}
                                            if($key == 'client_has_adopted'){echo "Specify details of client has adopted any schools";}
                                            if($key == 'approving_autorities'){echo "Who are the approving autorities of the proposal ?";}
                                            if($key == 'budget_for_cfyear'){echo "What is the left over budget for the current financial year ?";}
                                            if($key == 'fund_sanstion_limit'){echo "what is the fund sanction limit at their level ?";}
                                            if($key == 'other_specific_remarks'){echo "Any other specific remarks regards to the meeting ?";}
                                            if($key == 'submit_proposal'){echo "Do we need to submit the proposal ?";}
                                            if($key == 'proposal_no_of_school'){echo "Proposed number of schools";}
                                            if($key == 'proposal_of_budget'){echo "Proposed budget";}
                                            if($key == 'proposal_of_location'){echo "Proposed location";}
                                            if($key == 'identify_school'){echo "Do we need to identify school ?";}
                                            if($key == 'identify_school_state'){
                                              echo "Name of identify school";
                                            }
                                            if($key == 'permission_letter'){echo "School permission letter required ?";}
                                            if($key == 'permission_letter_rech'){echo "Letter should be address to whom in the organization, along with Name and designation and Location";}
                                            if($key == 'Letter_organization_name'){echo "Letter Organization Name";}
                                            if($key == 'Letter_organization_designation'){echo "Organization Designation Name";}
                                            if($key == 'Letter_organization_location'){echo "Organization Location Name";}
                                            if($key == 'client_int_school_visit'){echo "Client is interested for School Visit ?";}
                                            if($key == 'client_int_type_project'){echo "Type of project ?";}
                                            if($key == 'client_int_school_date'){echo "Date for Client is interested for School Visit ?";}
                                            if($key == 'client_int_school_state'){echo "State for Client is interested for School Visit ?";}
                                            if($key == 'client_int_school_district'){echo "District for Client is interested for School Visit ?";}
                                            if($key == 'client_int_no_of_school'){echo "Number of School for Client is interested for School Visit ?";}
                                            if($key == 'intervention_cm_pst_sh'){echo "Do you need intervention from Cluster/PST/ Sales Head ?";}
                                            if($key == 'rpmmom'){echo "Short MOM Remarks";}
                                            if($key == 'partner'){echo "Partner Type";}
                                            if($key == 'cdate'){echo "Submit Date";}
                                            if($key == 'approved_status'){echo "Approved Status";}
                                            if($key == 'approved_by'){echo "Approved By";}
                                            if($key == 'approved_date'){echo "Approved Date";}
                                           

                                             ?></td>
                                            <td><?php 
                                            // echo $value;
                                            if($key == 'ccstatus'){echo $ccstatusname;}
                                            if($key == 'action_id'){echo $action_name;}
                                            if($key == 'user_id'){echo $uname;}
                                            if($key == 'init_cmpid'){echo $compname;}
                                            if($key == 'actontaken'){echo $value;}
                                            if($key == 'meetingdonewinitiator'){echo $value;}
                                            if($key == 'presentation'){echo $value;}
                                            
                                            if($key == 'project_intervention_select'){echo $value;}
                                            if($key == 'project_intervention'){echo $value;}


                                            if($key == 'client_has_adopted_select'){echo $value;}
                                            if($key == 'client_has_adopted'){echo $value;}
                                            if($key == 'approving_autorities'){echo $value;}
                                            if($key == 'budget_for_cfyear'){echo $value;}
                                            if($key == 'fund_sanstion_limit'){echo $value;}
                                            if($key == 'other_specific_remarks'){echo $value;}
                                            if($key == 'submit_proposal'){echo $value;}
                                            if($key == 'proposal_no_of_school'){echo $value;}
                                            if($key == 'proposal_of_budget'){echo $value;}
                                            if($key == 'proposal_of_location'){echo $value;}
                                            if($key == 'identify_school'){echo $value;}

                                            if($key == 'identify_school_state'){
                                              foreach($combinedArray as $arrays){
                                                echo "Name of State - " . $arrays['identify_school_state'] . "<br>";
                                                echo "Name of District - " . $arrays['identify_school_district'] . "<br>";
                                                echo "No of School - " . $arrays['no_of_school'] . "<br><br>";
                                              }
                                            }
                                            
                                            if($key == 'permission_letter'){echo $value;}
                                            if($key == 'permission_letter_rech'){echo $value;}
                                            if($key == 'Letter_organization_name'){echo $value;}
                                            if($key == 'Letter_organization_designation'){echo $value;}
                                            if($key == 'Letter_organization_location'){echo $value;}
                                            if($key == 'client_int_school_visit'){echo $value;}
                                            if($key == 'client_int_type_project'){echo $value;}
                                            if($key == 'client_int_school_date'){echo $value;}
                                            if($key == 'client_int_school_state'){echo $value;}
                                            if($key == 'client_int_school_district'){echo $value;}
                                            if($key == 'client_int_no_of_school'){echo $value;}
                                            if($key == 'intervention_cm_pst_sh'){echo $value;}
                                            if($key == 'rpmmom'){echo $value;}
                                            if($key == 'partner'){echo $value;}
                                            if($key == 'cdate'){echo $value;}
                                            if($key == 'approved_status'){
                                              if($value == ''){
                                                echo "<span class='bg-warning p-2'>Pending</span>";
                                            }else if($value == 'Approved'){
                                              echo "<span class='bg-success p-2'>".$value."</span>"; 
                                            }else if($value == 'Reject'){
                                              echo "<span class='bg-danger p-2'>".$value."</span>";
                                            }else if($value == 'NO RP'){
                                              echo "<span class='bg-danger p-2'>".$value."</span>";
                                            }
                                          }
                                            if($key == 'approved_by'){
                                              if($value !=''){
                                                echo $this->Menu_model->get_userbyid($value)[0]->name;
                                              }
                                            }
                                              if($key == 'approved_date'){echo $value;}
                                           
                                            
                                            ?></td>
                                        </tr>
                                 <?php  $i++; endforeach;endforeach; ?> 
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
    <!-- /.container-fluid -->
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
        "responsive": false, pageLength: 50,"autoWidth": false,
        "buttons": ["csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
 

    </script>

  </body>
</html>